@extends('layouts.admin')

@section('title', 'Add Staff Member')

@section('content')
    <div class="page-header animate-in" style="max-width: 800px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-plus-circle"></i>
            Add New Staff
        </div>
        <div class="page-subtitle">Create a new account for your team member.</div>
    </div>

    <div class="section animate-in" style="max-width: 800px; margin: 0 auto;">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                @if($tenants ?? null)
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Shop / Tenant</label>
                        <select name="tenant_id" required id="tenant_id"
                            style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                            <option value="">Select shop</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                        @error('tenant_id') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                    </div>
                @endif
                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Full
                        Name</label>
                    <input type="text" name="name" required placeholder="e.g., John Doe"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('name') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Email
                        Address</label>
                    <input type="email" name="email" required placeholder="john@example.com"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('email') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label
                        style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Role</label>
                    <select name="role" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Branch
                        (Optional)</label>
                    <select name="branch_id" id="branch_id"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" data-tenant-id="{{ $branch->tenant_id ?? '' }}">{{ $branch->name }}{{ isset($tenants) ? ' (' . ($branch->tenant?->name ?? '') . ')' : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Password</label>
                    <input type="password" name="password" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('password') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Confirm
                        Password</label>
                    <input type="password" name="password_confirmation" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                </div>
            </div>

            <div
                style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--gray-100); padding-top: 20px;">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Staff Account</button>
            </div>
        </form>
    </div>
    @if($tenants ?? null)
    <script>
        document.getElementById('tenant_id').addEventListener('change', function () {
            var tid = this.value;
            var opts = document.querySelectorAll('#branch_id option[data-tenant-id]');
            opts.forEach(function (o) {
                o.style.display = (tid === '' || o.getAttribute('data-tenant-id') === tid) ? '' : 'none';
                if (tid !== '' && o.getAttribute('data-tenant-id') !== tid) o.disabled = true;
                else o.disabled = false;
            });
            document.getElementById('branch_id').value = '';
        });
    </script>
    @endif
@endsection