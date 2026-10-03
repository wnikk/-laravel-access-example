<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Wnikk\LaravelAccessRules\Xacml\Xacml;

/**
 * Example 14: XACML 3.0 from a controller. docs/tutorial-abac-step-by-step.md, example 17.
 * The routes need "can:manage-access".
 *
 * Xacml\Xacml takes any target and any source, so a controller is three lines. The console
 * does the same through "acr:xacml:export" and "acr:xacml:import".
 */
class Example14Controller extends Controller
{
    public function index()
    {
        return view('examples.xacml', ['report' => null]);
    }

    /**
     * The document is written as it is produced, one owner at a time, so memory stays flat.
     */
    public function download(Xacml $xacml)
    {
        return response()->streamDownload(fn () => $xacml->export('php://output'), 'access-rules.xml', ['Content-Type' => 'application/xml']);
    }

    /**
     * Look first: what the document would change, as a plan. Nothing is written.
     */
    public function check(Request $request, Xacml $xacml)
    {
        $request->validate(['policy' => 'required|file']);

        return view('examples.xacml', ['report' => $xacml->check($request->file('policy'))]);
    }

    /**
     * The same plan, executed. "replace" also brings what differs to the document.
     */
    public function import(Request $request, Xacml $xacml)
    {
        $request->validate(['policy' => 'required|file']);

        return Response::json($xacml->import($request->file('policy'), ['replace' => $request->boolean('replace')]));
    }
}
