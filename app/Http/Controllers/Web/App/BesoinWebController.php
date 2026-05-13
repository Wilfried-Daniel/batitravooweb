<?php

namespace App\Http\Controllers\Web\App;

use App\Models\Besoin;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BesoinWebController extends ShellController
{
    public function edit(Request $request, Besoin $besoin): View
    {
        $this->authorizeBesoin($request, $besoin);

        return $this->render($request, 'besoin_form', [
            'besoinFormMode' => 'edit',
            'routeBesoin' => $besoin,
            'title' => 'Modifier le besoin',
            'intro' => 'Mettez à jour votre chantier ou besoin publié — comme dans l’application mobile.',
        ]);
    }

    public function update(Request $request, Besoin $besoin): RedirectResponse
    {
        $this->authorizeBesoin($request, $besoin);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'budget' => ['nullable', 'string', 'max:128'],
            'start_label' => ['nullable', 'string', 'max:128'],
            'place' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'duration' => ['nullable', 'string', 'max:128'],
            'short_date' => ['nullable', 'string', 'max:64'],
            'status' => ['nullable', 'string', Rule::in(['open', 'in_progress', 'closed', 'cancelled'])],
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        if (array_key_exists('budget', $data) && is_string($data['budget']) && trim($data['budget']) === '') {
            $data['budget'] = null;
        }

        if ($request->hasFile('image')) {
            if ($besoin->image_path) {
                Storage::disk('public')->delete($besoin->image_path);
            }
            $data['image_path'] = $request->file('image')->store('besoins', 'public');
        }
        unset($data['image']);

        $besoin->fill($data);
        $besoin->save();

        $slug = (string) $request->segment(2);

        return redirect()->route('app.'.$slug.'.besoins')->with('status', 'Besoin mis à jour.');
    }

    public function destroy(Request $request, Besoin $besoin): RedirectResponse
    {
        $this->authorizeBesoin($request, $besoin);
        if ($besoin->image_path) {
            Storage::disk('public')->delete($besoin->image_path);
        }
        $besoin->delete();

        $slug = (string) $request->segment(2);

        return redirect()->route('app.'.$slug.'.besoins')->with('status', 'Besoin supprimé.');
    }

    private function authorizeBesoin(Request $request, Besoin $besoin): void
    {
        $u = $request->user();
        abort_unless(in_array($u->profile_type, [
            User::PROFILE_PARTICULIER,
            User::PROFILE_ENTREPRENEUR_BATIMENT,
        ], true), 403);
        abort_unless((int) $besoin->user_id === (int) $u->id, 404);
    }
}
