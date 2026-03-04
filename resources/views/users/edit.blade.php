@extends('layouts.admin')

@section('title', 'Edit Staff Member')

@section('content')
    <div class="page-header animate-in" style="max-width: 800px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-edit"></i>
            Edit Staff: {{ $user->name }}
        </div>
        <div class="page-subtitle">Update account details and permissions.</div>
    </div>

    <div class="section animate-in" style="max-width: 800px; margin: 0 auto;">
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Full
                        Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('name') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Email
                        Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('email') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label
                        style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Role</label>
                    <select name="role" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $role->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Branch
                        (Optional)</label>
                    <select name="branch_id"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $user->branch_id == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div
                    style="grid-column: span 2; padding: 10px; background: var(--light-blue-bg); border-radius: 8px; margin-top: 10px;">
                    <div style="font-weight: 700; color: var(--light-blue); font-size: 0.85rem; margin-bottom: 10px;">Change
                        Password (Leave blank to keep current)</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label
                                style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">New
                                Password</label>
                            <input type="password" name="password"
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                            @error('password') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label
                                style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Confirm
                                New Password</label>
                            <input type="password" name="password_confirmation"
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 10px; margin-top: 20px;">
                    <input type="checkbox" name="is_active" id="is_active" {{ $user->is_active ? 'checked' : '' }}
                        style="width: 18px; height: 18px;">
                    <label for="is_active" style="font-weight: 600; color: var(--gray-700);">Account is active</label>
                </div>
            </div>

            <div
                style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--gray-100); padding-top: 20px;">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Staff Account</button>
            </div>
        </form>
    </div>
@endsection