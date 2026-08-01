<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $search = (string) $request->query('search', '');

        $users = User::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            )
            ->orderByRaw("FIELD(role, 'admin', 'staff')")
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_admin' => $user->isAdmin(),
                'created_at' => $user->created_at?->format('d/m/Y'),
            ]);

        return Inertia::render('Users/Index', [
            'filters' => ['search' => $search],
            'users' => $users,
            'adminCount' => User::where('role', 'admin')->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateUser($request);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        User::create($data);

        return back()->with('success', 'ເພີ່ມຜູ້ໃຊ້ສຳເລັດ');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validateUser($request, $user);

        // Prevent demoting the last admin
        if ($user->isAdmin() && $data['role'] !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->withErrors(['role' => 'ບໍ່ສາມາດຫຼຸດສິດ Admin ສຸດທ້າຍໃນລະບົບໄດ້']);
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'ແກ້ໄຂຂໍ້ມູນຜູ້ໃຊ້ສຳເລັດ');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'ບໍ່ສາມາດລຶບບັນຊີຕົນເອງໄດ້');
        }

        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'ບໍ່ສາມາດລຶບ Admin ສຸດທ້າຍໃນລະບົບໄດ້');
        }

        $user->delete();

        return back()->with('success', 'ລຶບຜູ້ໃຊ້ສຳເລັດ');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $emailUnique = Rule::unique('users', 'email')->ignore($user?->id);
        $passwordRules = $user
            ? ['nullable', 'min:6', 'confirmed']
            : ['required', 'min:6', 'confirmed'];

        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', $emailUnique],
            'role' => ['required', Rule::in(['admin', 'staff'])],
            'password' => $passwordRules,
            'password_confirmation' => 'nullable',
        ], [
            'name.required' => 'ກະລຸນາໃສ່ຊື່',
            'email.required' => 'ກະລຸນາໃສ່ອີເມລ',
            'email.email' => 'ຮູບແບບອີເມລບໍ່ຖືກຕ້ອງ',
            'email.unique' => 'ອີເມລນີ້ຖືກໃຊ້ແລ້ວ',
            'role.required' => 'ກະລຸນາເລືອກສິດ',
            'role.in' => 'ສິດບໍ່ຖືກຕ້ອງ',
            'password.required' => 'ກະລຸນາໃສ່ລະຫັດຜ່ານ',
            'password.min' => 'ລະຫັດຜ່ານຕ້ອງມີຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ',
            'password.confirmed' => 'ລະຫັດຜ່ານບໍ່ຕົງກັນ',
        ])->validate();

        return [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => $validated['password'] ?? null,
        ];
    }
}
