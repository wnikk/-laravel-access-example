<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * The test bench checks itself: every example answers as its tutorial says.
 * The database lives in memory, see phpunit.xml.
 */
class ExamplesTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function ann(): User
    {
        return User::findOrFail(1);
    }

    private function nobody(): User
    {
        return User::findOrFail(3);
    }

    public function test_examples_of_the_first_tutorial(): void
    {
        $this->get('/example1')->assertForbidden();
        $this->actingAs($this->nobody())->get('/example1')->assertForbidden();

        $this->actingAs($this->ann());
        $this->get('/example1')->assertOk()->assertJsonCount(5);
        $this->get('/example2')->assertOk()->assertJsonPath('name', 'Ann');
        $this->post('/example3/name', ['name' => 'Ann'])->assertOk();
        $this->get('/example4')->assertOk()->assertJsonCount(3);
        $this->get('/example4/1')->assertOk();
        $this->get('/example5')->assertOk()->assertJsonCount(3);
        $this->get('/example5/1')->assertOk();

        // ".self": Ann wrote news 1 and 2, Bob wrote news 3
        $this->post('/example6/1', ['name' => 'First news'])->assertOk();
        $this->post('/example6/3', ['name' => 'Not mine'])->assertForbidden();

        // news of the last 48 hours: the second one is five days old
        $this->get('/example7')->assertOk()->assertJsonPath('*.id', [1, 3]);
        $this->get('/example7/1')->assertOk();
        $this->get('/example7/2')->assertForbidden();

        $this->actingAs($this->nobody())->get('/example5')->assertForbidden();
    }

    public function test_orders_of_the_seeded_state(): void
    {
        $this->get('/example8')->assertRedirect('/login');

        // Ann is in team North: clients Acme and Bolt
        $this->actingAs($this->ann());
        $this->get('/example8')->assertOk()->assertJsonPath('data.*.id', [1, 2, 5]);
        $this->get('/example8/1')->assertOk()->assertJsonPath('number', 'EU-1001');
        $this->get('/example8/3')->assertForbidden();

        // limit 500, not her own order
        $this->get('/example8/5/approve')->assertOk();
        $this->get('/example8/1/approve')->assertForbidden();

        // the option "csv" is granted, "pdf" is not; order 4 is locked, and the rule itself says "not locked"
        $this->get('/example8/1/export/csv')->assertOk();
        $this->get('/example8/1/export/pdf')->assertForbidden();
        $this->get('/example8/4/export/csv')->assertForbidden();

        $this->actingAs(User::findOrFail(2))->get('/example8')->assertJsonPath('data.*.id', [3]);
        $this->actingAs($this->nobody())->get('/example8')->assertJsonPath('data', []);
    }

    /**
     * Every scenario of the article: the list and the records agree, and both match the article.
     */
    public function test_scenarios_give_what_the_article_says(): void
    {
        Carbon::setTestNow(now()->startOfDay()->setTime(12, 0));

        $this->actingAs($this->nobody())->get('/example9/priority')->assertForbidden();
        $this->actingAs($this->ann());

        $scenarios = $this->get('/example9')->assertOk()->json();
        $this->assertGreaterThan(20, count($scenarios));

        foreach ($scenarios as $scenario) {
            $name   = basename($scenario['try']);
            $answer = $this->get('/example9/'.$name)->assertOk()->json();

            foreach ($answer['result'] as $user => $result) {
                $this->assertSame($result['listed'], $result['opened'], "$name, $user: the list and the records");

                if (is_array($result['expected'])) {
                    $this->assertSame($result['expected'], $result['listed'], "$name, $user: the article");
                }
            }
        }

        $this->get('/example9/reset')->assertOk();
        $this->get('/example8')->assertJsonPath('data.*.id', [1, 2, 5]);
    }

    public function test_menu_and_three_ways_to_ask(): void
    {
        $this->get('/example10')->assertOk()->assertDontSee('>Orders<', false);

        $this->actingAs($this->ann())->get('/example10')->assertOk()
            ->assertSee('>Orders<', false)
            ->assertSeeInOrder(['$order)', 'true', 'Order::class)', 'true', "can('orders.view')", 'false'], false);

        $this->actingAs($this->nobody())->get('/example10')->assertOk()->assertDontSee('>Orders<', false);
    }

    public function test_guests_see_the_catalogue(): void
    {
        $this->get('/example11')->assertOk()->assertJsonPath('*.name', ['Phone', 'Charger']);
        $this->get('/example11/1')->assertOk();
        $this->get('/example11/3')->assertForbidden();

        // a signed in user does not get permissions of the guest on top of its own
        $this->actingAs($this->nobody())->get('/example11')->assertOk()->assertExactJson([]);
    }

    public function test_a_refusal_names_itself_and_debug_mode_explains_it(): void
    {
        $this->actingAs($this->ann());

        // Without the mode first: a test sends all its requests through one application, and the
        // mode stays on until the end of it. Under PHP-FPM every request starts with the mode off.
        $this->get('/example12')->assertOk()->assertJsonPath('lists', []);
        $this->get('/example12/4')->assertForbidden()
            ->assertSee('This action is unauthorized.')
            ->assertSee('orders.view')
            ->assertDontSee('PROHIBITED');

        $this->get('/example12/4?access_debug=1')->assertForbidden()
            ->assertSee('This action is unauthorized.')
            ->assertSee('PROHIBITED')
            ->assertSee('order.locked');

        $this->get('/example12?access_debug=1')->assertOk()->assertJsonPath('lists.0.ability', 'orders.view');
    }

    public function test_explain_lint_and_audit_as_data(): void
    {
        $this->actingAs($this->nobody())->get('/example13/lint')->assertForbidden();
        $this->actingAs($this->ann());

        $this->get('/example13/explain/2/orders.view/4')->assertOk()
            ->assertJsonPath('decision', false)
            ->assertJsonPath('stale_cache', false);

        $this->get('/example13/lint')->assertOk()->assertExactJson(['problems' => [], 'fixed' => 0]);

        $this->get('/example9/priority');
        $this->assertStringContainsString('permission.granted', implode("\n", $this->get('/example13/audit')->assertOk()->json()));
    }

    /**
     * Example 18 of the article: the morph type travels in the subquery, so the category that
     * shares its id with the Battery pack never makes it "on sale", and the list agrees with the check.
     */
    public function test_polymorphic_relations(): void
    {
        $this->actingAs($this->nobody())->get('/example15/on-sale')->assertForbidden();
        $this->actingAs($this->ann());

        foreach ($this->get('/example15')->assertOk()->json() as $scenario) {
            $answer = $this->get('/example15/'.basename($scenario['try']))->assertOk()->json();

            $this->assertSame($answer['expected'], array_map('intval', array_keys($answer['listed'])), $scenario['what']);
            $this->assertSame($answer['expected'], $answer['opened'], $scenario['what']);
        }

        $this->assertStringContainsString('"taggable_type" = \'product\'', $this->get('/example15/on-sale')->json('sql'));
        $this->get('/example15/reset');
        $this->assertFalse($this->nobody()->can('products.view', \App\Models\Product::class));
    }

    public function test_xacml_export_comes_back_as_a_plan(): void
    {
        $this->actingAs($this->ann());
        $this->get('/example14')->assertOk();

        $document = $this->get('/example14/download')->assertOk()->assertHeader('Content-Type', 'application/xml')->streamedContent();
        $this->assertStringStartsWith('<?xml', $document);

        $upload = fn () => UploadedFile::fake()->createWithContent('access-rules.xml', $document);

        // nothing has changed since the export
        $this->post('/example14/check', ['policy' => $upload()])->assertOk()->assertSee('Plan, nothing was written')->assertDontSee('differs');

        // another scenario, and the same document differs from the database
        $this->get('/example9/priority');
        $this->post('/example14/check', ['policy' => $upload()])->assertOk()->assertSee('differs')->assertSee('order.client.team_id in user.tenant');

        // "replace" brings what differs back to the document. An import never deletes: what Ann got
        // from the scenario stays and is listed as "only_in_database".
        $this->post('/example14/import', ['policy' => $upload(), 'replace' => 1])->assertOk()->assertJsonPath('written', true);
        $this->post('/example14/check', ['policy' => $upload()])->assertOk()->assertDontSee('differs')->assertSee('only_in_database');
    }
}
