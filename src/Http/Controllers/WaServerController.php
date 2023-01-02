<?php

namespace Quods\Whatsapp\Http\Controllers;

use Quods\Whatsapp\Models\WaServer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class WaServerController extends Controller
{
    /**
     * View all data Server
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(WaServer::query())->toJson();
        }

        return view('wafiture::server.index');
    }

    public function show(Request $request, WaServer $waServer)
    {
        return $waServer;
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'api_key' => 'required',
            'ip' => 'required',
            'port' => 'required',
            'status' => 'required|in:enable,disable',
            'max_devices' => 'required|integer',
        ]);

        if ($request->wa_server_id) {
            $request->validate(['wa_server_id' => 'exists:wa_servers,id']);
            $WaServer = WaServer::find($request->wa_server_id);
        } else {
            $WaServer = new WaServer();
        }

        $WaServer->name = $request->name;
        $WaServer->ip = $request->ip;
        $WaServer->port = $request->port;
        $WaServer->status = $request->status;
        $WaServer->max_devices = $request->max_devices ?? 0;
        $WaServer->disable_ssl_check = $request->disable_ssl_check ?? 0; // defult 0 (allow ssl check)
        $WaServer->api_key = $request->api_key;
        $WaServer->save();

        $msg = "Server {$WaServer->name} has been saved";
        if ($request->ajax()) {
            return response()->json([
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function toggle_status(Request $request)
    {
        $WaServer = WaServer::findOrFail($request->id);
        $WaServer->status == 'enable' ?
            $WaServer->makeDisable() :
            $WaServer->makeEnable();
        $WaServer->refresh();
        $msg = "Server {$WaServer->name} has been {$WaServer->status}d!";
        if ($request->ajax()) {
            return response()->json([
                'status' => $WaServer->status,
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }
}
