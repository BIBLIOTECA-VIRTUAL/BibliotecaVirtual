<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\EditProfileRequest;
use App\Http\Resources\UsuarioResource;
use App\Http\Resources\UserResource;
use App\Models\Usuario;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Lista todos os usuários (apenas admin)
     */
    public function index()
    {
        try {
            $this->authorize('viewAny', User::class);

            $usuarios = User::orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'data' => UserResource::collection($usuarios)
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar usuários: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar usuários',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cria novo usuário (apenas admin)
     */
    public function store(UsuarioRequest $request)
    {
        try {
            $this->authorize('create', User::class);

            $usuario = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'perfil_id' => $request->perfil_id,
                'date_birthday' => $request->date_birthday,
                'gender' => $request->gender
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Usuário criado com sucesso',
                'data' => new UserResource($usuario)
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao criar usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza usuário existente
     */
    public function update(UsuarioRequest $request, string $id)
    {
        try {
            $usuario = User::findOrFail($id);
            $this->authorize('update', $usuario);

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'date_birthday' => $request->date_birthday,
                'gender' => $request->gender
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            if ($request->filled('perfil_id')) {
                $updateData['perfil_id'] = $request->perfil_id;
            }

            $usuario->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Usuário atualizado com sucesso',
                'data' => new UserResource($usuario)
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar usuário: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove um usuário
     */
    public function destroy(string $id)
    {
        try {
            $usuario = User::findOrFail($id);
            $this->authorize('delete', $usuario);

            $usuario->tokens()->delete(); // Remove todos os tokens do usuário
            $usuario->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Usuário deletado com sucesso'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao deletar usuário: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao deletar usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Realiza login do usuário
     */
    public function login(Request $request)
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

    /**
     * Realiza logout do usuário
     */
    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout realizado com sucesso'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro no logout: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao realizar logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registra novo usuário
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'date_birthday' => $request->date_birthday,
                'gender' => $request->gender,
                'perfil_id' => 0 // Usuário comum por padrão
            ]);

            // Cria token para o novo usuário
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Usuário registrado com sucesso',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erro no registro: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao registrar usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna perfil do usuário autenticado
     */
    public function profile()
    {
        try {
            $user = auth()->user();

            return response()->json([
                'status' => 'success',
                'data' => new UserResource($user)
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar perfil: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza perfil do usuário
     */
    public function editProfile(EditProfileRequest $request)
    {
        try {
            $user = auth()->user();

            $updateData = [
                'name' => $request->name,
                'date_birthday' => $request->date_birthday,
                'gender' => $request->gender
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
                $user->tokens()->delete(); // Revoga tokens ao mudar senha
            }

            $user->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Perfil atualizado com sucesso',
                'data' => new UserResource($user)
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar perfil: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
