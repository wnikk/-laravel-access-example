<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The panel of wnikk/laravel-access-ui on this application: the page inside the layout of the
 * sandbox, every screen answering, and the widget on the profile page.
 */
class AccessUiTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_the_panel_is_behind_the_admin_gate(): void
    {
        $this->get('/access-control')->assertRedirect('/login');
        $this->actingAs(User::findOrFail(3))->get('/access-control')->assertForbidden();
    }

    public function test_every_screen_answers_for_the_administrator(): void
    {
        $this->actingAs(User::findOrFail(1));
        $manager = \Wnikk\LaravelAccessRules\Facades\Access::for('Role', 'manager')->record()->getKey();

        $this->get('/access-control')->assertOk()->assertSee('Access control')->assertSee('accessUiPanel', false)->assertSee('/vendor/accessui/accessUi.js', false);

        $this->getJson('/access-control/rules')->assertOk()->assertJsonPath('ok', true)->assertJsonFragment(['guard_name' => 'orders.view']);
        $this->getJson('/access-control/owners')->assertOk()->assertJsonFragment(['title' => 'Managers']);
        $this->getJson('/access-control/owners/'.$manager.'/permissions')->assertOk()->assertJsonPath('data.owner.title', 'Managers');
        $this->getJson('/access-control/owners/'.$manager.'/inherit?direction=children')->assertOk()->assertJsonFragment(['title' => 'Ann']);
        $this->getJson('/access-control/conditions/vocabulary')->assertOk()->assertJsonPath('data.resources.order.model', \App\Models\Order::class);
        $this->getJson('/access-control/explain?owner='.$manager.'&ability=orders.view&record=order:4')->assertOk()->assertJsonPath('data.decision', false);
        $this->getJson('/access-control/health')->assertOk()->assertJsonPath('data.problems', []);
        $this->get('/access-control/xacml/export')->assertOk()->assertHeader('Content-Type', 'application/xml');
    }

    public function test_the_widget_is_on_the_profile_page(): void
    {
        $this->actingAs(User::findOrFail(1))->get('/user')->assertOk()->assertSee('wacu-widget-root', false)->assertSee('var method  = "widget"', false);
    }

    /**
     * The list of users is where a check of the card starts: every account, its profile with the
     * card, a way to sign in as it. An account without the ability of the panel sees neither.
     */
    public function test_the_users_list_leads_to_every_profile_with_the_card(): void
    {
        $this->get('/users')->assertRedirect('/login');
        $this->actingAs(User::findOrFail(3))->get('/users')->assertForbidden();

        $this->actingAs(User::findOrFail(1));
        $page = $this->get('/users')->assertOk();

        foreach (User::orderBy('id')->get() as $user) {
            $page->assertSee('/user/'.$user->getKey(), false)->assertSee('/sign-in/'.$user->getKey(), false);
            $this->get('/user/'.$user->getKey())->assertOk()->assertSee('wacu-widget-root', false);
        }
    }

    /**
     * The panel follows the locale of the application. The package ships English and a way in:
     * messages put on window.accessUiMessages before its bundle, errors.php and lang/{locale}.json
     * on the server. The Russian itself lives in this application, under lang/.
     */
    public function test_the_panel_speaks_the_language_of_the_session(): void
    {
        $this->actingAs(User::findOrFail(1));

        $this->get('/lang/ru')->assertRedirect();
        $page = $this->withSession(['locale' => 'ru'])->get('/access-control')->assertOk();
        $page->assertSee('window.accessUiMessages = { ru: {', false)->assertSee('"app.title": "Управление доступом"', false)->assertSee('"locale":"ru"', false);

        // A refusal of the core comes back in Russian, the words of the core next to it.
        $rule = \Wnikk\LaravelAccessRules\Models\Rule::where('guard_name', 'orders.view')->firstOrFail();
        $this->withSession(['locale' => 'ru'])->deleteJson('/access-control/rules/'.$rule->getKey())
            ->assertStatus(403)->assertJsonPath('code', 'rule_managed_by_code')->assertJsonPath('message', 'Правило пришло с кодом. Его меняет миграция; здесь можно поправить заголовок и опции.');

        // English again, without the script: the page carries nothing it does not use.
        $this->withSession(['locale' => 'en'])->get('/access-control')->assertOk()->assertDontSee('accessUiMessages', false);
        $this->get('/lang/xx')->assertNotFound();
    }
}
