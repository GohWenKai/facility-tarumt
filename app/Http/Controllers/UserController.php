<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = \App\Models\User::query();

        // Search Logic
        if ($request->has('search')) {
            $search = $request->get('search');

            $query->whereIn('role', ['lecturer', 'student'])
                ->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('tarumt_id', 'like', "%{$search}%");
                });
        }

        $users = $query->orderBy('created_at', 'desc')->whereIn('role', ['lecturer', 'student'])->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        // This loads your separate 'update.blade.php' file
        return view('admin.users.update', compact('user'));
    }

    public function credits($id)
    {
        // 1. Find the user first
        $user = User::findOrFail($id);

        // 2. Check if the user is allowed to have credits reset
        // We use in_array to check if their role is valid
        if (!in_array($user->role, ['student', 'lecturer'])) {
            return back()->with('error', 'You can only reset credits for students or lecturers.');
        }

        // 3. Reset credits
        $user->credits = 10;
        $user->save();

        return back()->with('success', 'Credits reset successfully!');
    }

    public function update(Request $request, $id)
    {
       $user = User::findOrFail($id);

        // 1. Validate Input
        $request->validate([
            'name'  => 'required|string|max:255',
            // Ensure email is unique but ignore the current user's own email
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'address' => 'required|string|max:2000',
            'tel' => [
                'required',
                'string',
                'max:15', 
                'regex:/^\+60\d{9,10}$/' 
            ],
        ]);

        // 2. Update Basic Info
        $user->name = $request->name;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->tel = $request->tel;

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    public function destroy($id)
    {
        // 1. Find the user
        $user = User::findOrFail($id);

        // Optional: Prevent deleting yourself (if you are logged in as Admin)
        if (auth()->id() == $user->id) {
            return back()->with('error', 'You cannot delete your own account while logged in.');
        }

        // 2. Delete the user
        // Note: If you have Foreign Keys (like bookings), you might need to delete them first 
        // or ensure your database is set to 'ON DELETE CASCADE'.
        $user->delete();

        // 3. Redirect
        return back()->with('success', 'User deleted successfully.');
    }

    // 1. Show the Create Form
    public function create()
    {
        return view('admin.users.create');
    }

    // 2. Handle the Form Submission
    public function store(Request $request)
    {
        // Validate Input
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'tarumt_id' => 'required|string|max:20|unique:users',
            // ENFORCE ROLE: Only 'student' or 'lecturer' allowed
            'role'      => 'required|string|in:student,lecturer', 
            'tel'       => 'required|string|max:15',
            'address'   => 'required|string|max:255',
            'password'  => 'required|string|min:8|confirmed', // expects password_confirmation field
        ]);

        // Create the User
        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'tarumt_id' => $request->tarumt_id,
            'role'      => $request->role,
            'tel'       => $request->tel,
            'address'   => $request->address,
            'password'  => Hash::make($request->password), // Always Hash passwords
            'credits'   => 10, // Default credits (based on your previous logic)
        ]);
        
        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully!');
    }
}