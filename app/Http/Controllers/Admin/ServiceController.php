<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $q = Service::query()->with(['user', 'category']);

        if ($request->filled('service_kind')) {
            $q->where('service_kind', $request->string('service_kind'));
        }
        if ($search = $request->string('q')->trim()) {
            $q->where('title', 'like', "%{$search}%");
        }

        $services = $q->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.services.index', ['services' => $services]);
    }

    public function show(Service $service): View
    {
        $service->load('user', 'category');

        return view('admin.services.show', ['service' => $service]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);
        $service->update($data);

        return redirect()->route('admin.services.show', $service)->with('ok', 'Notes enregistrées.');
    }
}
