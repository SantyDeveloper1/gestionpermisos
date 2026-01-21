<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Mostrar la página de perfil del usuario
     */
    public function index()
    {
        $user = Auth::user();

        return view('docente.profile.profile', compact('user'));
    }

    /**
     * Actualizar el perfil del usuario
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female,other'],
            'document_type' => ['nullable', 'string', 'max:20'],
            'document_number' => ['nullable', 'string', 'max:20'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $data = [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
        ];


        // Manejar la imagen si se subió una nueva
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Generar nombre único para la imagen
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Ruta donde se guardará la imagen
            $destinationPath = public_path('storage/usuarios');

            // Crear el directorio si no existe
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Eliminar la imagen anterior si existe
            if ($user->image) {
                $oldImagePath = public_path('storage/' . $user->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Mover la imagen al directorio público
            $image->move($destinationPath, $imageName);

            // Guardar solo la ruta relativa en la BD
            $data['image'] = 'usuarios/' . $imageName;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente.',
            'user' => $user
        ]);
    }
}
