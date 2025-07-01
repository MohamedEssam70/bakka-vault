<?php

namespace App\Http\Controllers;

use App\Models\Password;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class VaultsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the vault with all passwords
     */
    public function index()
    {
        $passwords = Auth::user()->passwords()
            ->orderBy('name')
            ->get()
            ->map(function ($password) {
                return [
                    'id' => $password->id,
                    'name' => $password->name,
                    'username' => $password->username,
                    'email' => $password->email,
                    'url' => $password->url,
                    'category' => $password->category,
                    'color' => $password->color ?: '#007bff',
                    'icon' => $password->icon,
                    'created_at' => $password->created_at->format('M d, Y'),
                    'updated_at' => $password->updated_at->format('M d, Y')
                ];
            });

        return view('content.vault.index', compact('passwords'));
    }

    /**
     * Get password details via AJAX
     */
    public function show(Vault $password)
    {
        // Ensure user owns this password
        if ($password->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $decryptedPassword = Crypt::decrypt($password->password);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to decrypt password'], 500);
        }

        return response()->json([
            'id' => $password->id,
            'name' => $password->name,
            'username' => $password->username,
            'email' => $password->email,
            'phone' => $password->phone,
            'password' => $decryptedPassword,
            'url' => $password->url,
            'notes' => $password->notes,
            'icon' => $password->icon,
            'color' => $password->color ?: '#007bff',
            'category' => $password->category,
            'tags' => $password->tags ? explode(',', $password->tags) : [],
            'created_at' => $password->created_at->format('M d, Y H:i'),
            'updated_at' => $password->updated_at->format('M d, Y H:i')
        ]);
    }

    /**
     * Show the form for creating a new password
     */
    public function create()
    {
        return view('content.vault.create');
    }

    /**
     * Store a newly created password
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:1',
            'url' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:2000',
            'icon' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:500'
        ]);

        try {
            $password = Vault::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Crypt::encrypt($request->password),
                'url' => $request->url,
                'notes' => $request->notes,
                'icon' => $request->icon,
                'color' => $request->color ?: '#007bff',
                'category' => $request->category,
                'tags' => $request->tags,
                'user_id' => Auth::id(),
                'created_by' => Auth::user()->name
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password created successfully',
                    'password' => [
                        'id' => $password->id,
                        'name' => $password->name,
                        'username' => $password->username,
                        'email' => $password->email,
                        'url' => $password->url,
                        'category' => $password->category,
                        'color' => $password->color,
                        'icon' => $password->icon
                    ]
                ]);
            }

            return redirect()->route('vault.index')->with('success', 'Password created successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create password'
                ], 500);
            }

            return back()->withInput()->withErrors(['error' => 'Failed to create password']);
        }
    }

    /**
     * Show the form for editing the specified password
     */
    public function edit(Vault $password)
    {
        // Ensure user owns this password
        if ($password->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $password->decrypted_password = Crypt::decrypt($password->password);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to decrypt password']);
        }

        return view('content.vault.edit', compact('password'));
    }

    /**
     * Update the specified password
     */
    public function update(Request $request, Vault $password)
    {
        // Ensure user owns this password
        if ($password->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:1',
            'url' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:2000',
            'icon' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:500'
        ]);

        try {
            $password->update([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Crypt::encrypt($request->password),
                'url' => $request->url,
                'notes' => $request->notes,
                'icon' => $request->icon,
                'color' => $request->color ?: '#007bff',
                'category' => $request->category,
                'tags' => $request->tags,
                'updated_by' => Auth::user()->name
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password updated successfully'
                ]);
            }

            return redirect()->route('vault.index')->with('success', 'Password updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update password'
                ], 500);
            }

            return back()->withInput()->withErrors(['error' => 'Failed to update password']);
        }
    }

    /**
     * Remove the specified password
     */
    public function destroy(Vault $password)
    {
        // Ensure user owns this password
        if ($password->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $password->update(['deleted_by' => Auth::user()->name]);
            $password->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete password'
            ], 500);
        }
    }

    /**
     * Generate a secure password
     */
    public function generatePassword(Request $request)
    {
        $request->validate([
            'length' => 'integer|min:4|max:128',
            'uppercase' => 'boolean',
            'lowercase' => 'boolean',
            'numbers' => 'boolean',
            'symbols' => 'boolean'
        ]);

        $length = $request->input('length', 16);
        $includeUppercase = $request->boolean('uppercase', true);
        $includeLowercase = $request->boolean('lowercase', true);
        $includeNumbers = $request->boolean('numbers', true);
        $includeSymbols = $request->boolean('symbols', true);

        $characters = '';
        if ($includeLowercase) $characters .= 'abcdefghijklmnopqrstuvwxyz';
        if ($includeUppercase) $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($includeNumbers) $characters .= '0123456789';
        if ($includeSymbols) $characters .= '!@#$%^&*()_+-=[]{}|;:,.<>?';

        if (empty($characters)) {
            return response()->json(['error' => 'At least one character type must be selected'], 400);
        }

        $password = '';
        $charactersLength = strlen($characters);
        
        // Ensure at least one character from each selected type
        if ($includeLowercase) $password .= substr('abcdefghijklmnopqrstuvwxyz', random_int(0, 25), 1);
        if ($includeUppercase) $password .= substr('ABCDEFGHIJKLMNOPQRSTUVWXYZ', random_int(0, 25), 1);
        if ($includeNumbers) $password .= substr('0123456789', random_int(0, 9), 1);
        if ($includeSymbols) $password .= substr('!@#$%^&*()_+-=[]{}|;:,.<>?', random_int(0, 21), 1);

        // Fill the rest randomly
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $characters[random_int(0, $charactersLength - 1)];
        }

        // Shuffle the password to avoid predictable patterns
        $password = str_shuffle($password);

        return response()->json(['password' => $password]);
    }

    /**
     * Search passwords
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return response()->json([]);
        }

        $passwords = Auth::user()->passwords()
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('username', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('url', 'LIKE', "%{$query}%")
                  ->orWhere('category', 'LIKE', "%{$query}%")
                  ->orWhere('tags', 'LIKE', "%{$query}%")
                  ->orWhere('notes', 'LIKE', "%{$query}%");
            })
            ->orderBy('name')
            ->get()
            ->map(function ($password) {
                return [
                    'id' => $password->id,
                    'name' => $password->name,
                    'username' => $password->username ?: $password->email,
                    'email' => $password->email,
                    'url' => $password->url,
                    'category' => $password->category,
                    'color' => $password->color ?: '#007bff',
                    'icon' => $password->icon
                ];
            });

        return response()->json($passwords);
    }

    /**
     * Get password categories for filtering
     */
    public function getCategories()
    {
        $categories = Auth::user()->passwords()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return response()->json($categories);
    }

    /**
     * Filter passwords by category
     */
    public function filterByCategory(Request $request)
    {
        $category = $request->input('category');
        
        $query = Auth::user()->passwords()->orderBy('name');
        
        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }
        
        $passwords = $query->get()->map(function ($password) {
            return [
                'id' => $password->id,
                'name' => $password->name,
                'username' => $password->username ?: $password->email,
                'email' => $password->email,
                'url' => $password->url,
                'category' => $password->category,
                'color' => $password->color ?: '#007bff',
                'icon' => $password->icon
            ];
        });

        return response()->json($passwords);
    }

    /**
     * Check password strength
     */
    public function checkPasswordStrength(Request $request)
    {
        $password = $request->input('password');
        
        if (empty($password)) {
            return response()->json(['strength' => 0, 'feedback' => 'Enter a password']);
        }

        $score = 0;
        $feedback = [];

        // Length check
        if (strlen($password) >= 12) {
            $score += 2;
        } elseif (strlen($password) >= 8) {
            $score += 1;
        } else {
            $feedback[] = 'Use at least 8 characters';
        }

        // Character variety checks
        if (preg_match('/[a-z]/', $password)) $score += 1;
        else $feedback[] = 'Add lowercase letters';

        if (preg_match('/[A-Z]/', $password)) $score += 1;
        else $feedback[] = 'Add uppercase letters';

        if (preg_match('/[0-9]/', $password)) $score += 1;
        else $feedback[] = 'Add numbers';

        if (preg_match('/[^a-zA-Z0-9]/', $password)) $score += 1;
        else $feedback[] = 'Add special characters';

        // Common patterns check
        if (!preg_match('/(.)\1{2,}/', $password)) $score += 1;
        else $feedback[] = 'Avoid repeating characters';

        $strength = min(5, $score);
        $strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'][$strength];

        return response()->json([
            'strength' => $strength,
            'strengthText' => $strengthText,
            'feedback' => $feedback
        ]);
    }
}