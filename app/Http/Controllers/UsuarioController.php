<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Http\Resources\UsuarioResource;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        return UsuarioResource::collection(Usuario::all());
    }

    public function store(UsuarioRequest $request)
    {
        $usuario = Usuario::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'senha' => Hash::make($request->senha),
            'perfil_id' => $request->perfil_id,
        ]);

        return (new UsuarioResource($usuario))->response()->setStatusCode(201);
    }

    public function update(UsuarioRequest $request, string $id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->update($request->only(['nome', 'email']));

        return (new UsuarioResource($usuario))->response()->setStatusCode(200);
    }

    public function destroy(string $id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return response()->json(['mensagem' => 'Usuário deletado com sucesso.'], 200);
    }

    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                "data" => [
                    "message" => "Authorized",
                    "token" => $request->user()->createToken('login')->plainTextToken
                ]
            ], 200);
        }

        return response()->json(
            [
                "errors" => [
                    'message' => 'Not Authorized'
                ],
            ],
            403
        );
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json(
            [
                "data" => [
                    'message' => 'Token was Revoked'
                ],
            ],
            200
        );
        
    }

    public function register(RegisterRequest $request)
    {
        $request['password'] = bcrypt($request->password);
        $user = User::create($request->only('name', 'email', 'password', "date_birthday", "gender"));
        $user->assignProfile('person');

        return response()->json(
            [
                "data" => [
                    'message' => 'User Registered Successfully'
                ],
            ],
            201
        );
    }

    public function profile()
    {
        return new UserResource(User::find(auth()->user()->id)->load('profile'));
    }

    public function editProfile(EditProfileRequest $request)
    {
        $user = User::find(auth()->user()->id);

        if ($request->password) {
            $request['password'] = bcrypt($request->password);
            auth()->user()->currentAccessToken()->delete();
        }

        $user->update($request->only('name', 'password', "date_birthday", "gender"));

        return response()->json(
            [
                "data" => [
                    'message' => 'Successfully Edit User Profile'
                ],
            ],
            200
        );
    }
}
