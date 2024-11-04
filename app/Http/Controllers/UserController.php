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
    try {
        // Validação dos campos
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'Digite um email válido',
            'password.required' => 'O campo senha é obrigatório'
        ]);

        // Log da tentativa de login
        Log::info('Tentativa de login para email: ' . $request->email);

        // Verifica se o usuário existe
        $user = User::where('email', $request->email)->first();
        
        // Debug para verificar os dados
        Log::debug('Dados da requisição:', [
            'email' => $request->email,
            'password_provided' => !empty($request->password),
            'user_exists' => !is_null($user),
            'password_matches' => $user ? Hash::check($request->password, $user->password) : false
        ]);

        if (!$user || !Hash::check($request->password, $user->password)) {
            Log::info('Credenciais inválidas para o email: ' . $request->email);
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciais inválidas',
                'errors' => ['email' => ['Email ou senha incorretos']]
            ], 401);
        }

        // Revoga tokens anteriores
        $user->tokens()->delete();

        // Cria novo token
        $token = $user->createToken('auth-token')->plainTextToken;

        Log::info('Login bem-sucedido para usuário: ' . $user->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Login realizado com sucesso',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
                'perfil_id' => $user->perfil_id
            ]
        ]);
    } catch (ValidationException $e) {
        Log::error('Erro de validação no login: ' . json_encode($e->errors()));
        return response()->json([
            'status' => 'error',
            'message' => 'Erro de validação',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Erro no login: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Erro ao realizar login',
            'error' => $e->getMessage()
        ], 500);
    }
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