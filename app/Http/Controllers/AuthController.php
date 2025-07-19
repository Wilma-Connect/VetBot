<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'pays_id' => 'nullable|exists:pays,id',
                'role' => 'required|in:eleveur,veterinaire',
                'experience' => 'required',
            ]);

            $user = User::create([
                'nom' => $validatedData['nom'],
                'prenom' => $validatedData['prenom'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'pays_id' => $validatedData['pays_id'] ?? null,
                'role' => $validatedData['role'],
                'numerotelephone' => $request->numerotelephone,
                'ville' => $request->ville,
                'experience' => $validatedData['experience'],
                'type_elevage' => $request->type_elevage,
                'quantite' => $request->quantite,
                'localisation' => $request->localisation,
                'surface_m2' => $request->surface_m2,
            ]);

            return response()->json($user, 201);

        } catch (\Exception $e) {
            Log::error('Erreur enregistrement utilisateur : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l’enregistrement'], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ["Les informations d'identification fournies sont incorrectes."],
            ]);
        }

        return response()->json([
            'token' => $user->createToken('authToken')->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie']);
    }
}
