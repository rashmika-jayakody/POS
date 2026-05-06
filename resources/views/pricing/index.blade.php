@extends('layouts.admin')

@section('title', __('Pricing Plans'))

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-tags"></i>
            {{ __('Pricing Plans') }}
        </div>
        <div class="page-subtitle">
            {{ __('Choose the best plan for your growing business.') }}
        </div>
    </div>

    @if($isSystemOwner)
        <div class="stats-grid animate-in" style="margin-bottom: 32px;">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-label">{{ __('Total Active Shops') }}</div>
                <div class="stat-value">{{ array_sum($stats) }}</div>
            </div>
            @foreach($plans as $key => $plan)
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(100, 116, 139, 0.1); color: var(--gray-500);"><i class="fas fa-{{ $key === 'starter' ? 'seedling' : ($key === 'growth' ? 'chart-line' : 'building') }}"></i></div>
                    <div class="stat-label">{{ $plan['name'] }}</div>
                    <div class="stat-value">{{ $stats[$key] ?? 0 }}</div>
                </div>
            @endforeach
        </div>
        
        <div class="section animate-in" style="margin-bottom: 32px;">
            <h2 class="section-title"><i class="fas fa-tools"></i> {{ __('Plan Management') }}</h2>
            <p style="color: var(--gray-500); margin-bottom: 16px;">{{ __('To change a specific shop\'s plan, please visit the Registered Shops management page.') }}</p>
            <a href="{{ route('tenants.index') }}" class="btn btn-primary">
                <i class="fas fa-store-alt"></i> {{ __('Go to Registered Shops') }}
            </a>
        </div>
    @endif

    @if(!$isSystemOwner && session('success'))
        <div style="background: #d1fae5; border: 1px solid #86efac; color: #065f46; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="stats-grid animate-in" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        @php $previousFeatures = []; @endphp
        @foreach($plans as $plan)
            @php
                $key = $plan->slug;
                $isCurrent = !$isSystemOwner && ($currentPlan === $key);
                $isGrowth = ($key === 'growth');
                
                // Logic to show unique features (Pro features)
                $uniqueFeatures = array_diff($plan->features, $previousFeatures);
                $previousFeatures = array_unique(array_merge($previousFeatures, $plan->features));
                
                $displayFeatures = $isSystemOwner ? $plan->features : array_slice($uniqueFeatures, 0, 8);
            @endphp
            <div class="stat-card {{ $isGrowth ? 'blue' : '' }}" style="position: relative; border: {{ $isCurrent ? '2px solid var(--light-blue)' : '1px solid rgba(74, 158, 255, 0.1)' }}; display: flex; flex-direction: column;">
                @if($isCurrent)
                    <div style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--light-blue); color: white; padding: 2px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">
                        {{ __('CURRENT PLAN') }}
                    </div>
                @endif
                
                @if($isSystemOwner)
                    <button class="btn btn-secondary btn-sm" style="position: absolute; top: 12px; right: 12px; padding: 4px 8px; font-size: 0.75rem;" onclick="openEditModal('{{ $plan->slug }}', '{{ addslashes($plan->name) }}', '{{ addslashes($plan->description) }}', {{ $plan->price_lkr }}, {{ $plan->max_branches }}, {{ $plan->max_users }}, {{ json_encode($plan->features) }})">
                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                    </button>
                @endif

                <div class="stat-icon" style="background: {{ $isGrowth ? 'rgba(74, 158, 255, 0.2)' : 'rgba(100, 116, 139, 0.1)' }}; color: {{ $isGrowth ? 'var(--light-blue)' : 'var(--gray-500)' }};">
                    <i class="fas fa-{{ $key === 'starter' ? 'seedling' : ($key === 'growth' ? 'chart-line' : 'building') }}"></i>
                </div>
                
                <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--navy-dark); margin-bottom: 4px;">{{ $plan->name }}</h3>
                <p style="color: var(--gray-500); font-size: 0.85rem; margin-bottom: 20px;">{{ $plan->description }}</p>
                
                <div style="margin-bottom: 24px;">
                    @if($plan->slug === 'custom')
                        <span style="font-size: 2rem; font-weight: 800; color: var(--navy-dark);">{{ __('Custom') }}</span>
                        <span style="color: var(--gray-500); font-size: 0.9rem;">/{{ __('Tailored') }}</span>
                    @else
                        <span style="font-size: 2rem; font-weight: 800; color: var(--navy-dark);">LKR {{ number_format($plan->price_lkr) }}</span>
                        <span style="color: var(--gray-500); font-size: 0.9rem;">/{{ __('month') }}</span>
                    @endif
                </div>

                <div style="flex-grow: 1;">
                    <ul style="list-style: none; padding: 0; margin-bottom: 24px;">
                        <li style="padding: 8px 0; border-bottom: 1px solid var(--gray-100); font-size: 0.9rem; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-check-circle" style="color: var(--success); font-size: 0.8rem;"></i>
                            <span>{{ $plan->max_branches == -1 ? __('Unlimited Stores') : ($plan->max_branches . ' ' . ($plan->max_branches == 1 ? __('Store') : __('Stores'))) }}</span>
                        </li>
                        <li style="padding: 8px 0; border-bottom: 1px solid var(--gray-100); font-size: 0.9rem; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-check-circle" style="color: var(--success); font-size: 0.8rem;"></i>
                            <span>{{ $plan->max_users == -1 ? __('Unlimited Users') : ($plan->max_users . ' ' . ($plan->max_users == 1 ? __('User') : __('Users'))) }}</span>
                        </li>
                        @foreach($displayFeatures as $feature)
                            <li style="padding: 8px 0; border-bottom: 1px solid var(--gray-100); font-size: 0.9rem; display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-check-circle" style="color: var(--success); font-size: 0.8rem;"></i>
                                <span>{{ ucwords(str_replace('_', ' ', $feature)) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if($isSystemOwner)
                    <div style="text-align: center; color: var(--gray-400); font-size: 0.85rem; padding: 12px; background: var(--gray-50); border-radius: 8px;">
                        <i class="fas fa-chart-pie"></i> {{ $stats[$plan->slug] ?? 0 }} {{ __('Active Shops') }}
                    </div>
                @elseif(!$isCurrent)
                    @if($plan->slug === 'custom')
                        <a href="mailto:hello@supergrocerspos.com" class="btn btn-secondary" style="width: 100%; justify-content: center; padding: 12px;">
                            {{ __('Contact Sales') }}
                        </a>
                    @else
                        <button class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px;" onclick="alert('Upgrade functionality coming soon!')">
                            {{ __('Upgrade to :name', ['name' => $plan->name]) }}
                        </button>
                    @endif
                @else
                    <button class="btn" style="width: 100%; justify-content: center; padding: 12px; background: var(--gray-100); color: var(--gray-500); cursor: default;" disabled>
                        {{ __('Your Current Plan') }}
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    @if($isSystemOwner)
        <!-- Edit Plan Modal -->
        <div id="editPlanModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
            <div style="background: white; border-radius: 12px; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="font-size: 1.25rem; font-weight: 700;"><i class="fas fa-edit"></i> {{ __('Edit Plan Details') }}</h2>
                    <button onclick="closeEditModal()" style="border: none; background: none; color: var(--gray-400); cursor: pointer; font-size: 1.25rem;"><i class="fas fa-times"></i></button>
                </div>
                
                <form id="editPlanForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 4px;">{{ __('Plan Name') }}</label>
                            <input type="text" name="name" id="planName" style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-200); border-radius: 6px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 4px;">{{ __('Price (LKR)') }}</label>
                            <input type="number" name="price_lkr" id="planPrice" style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-200); border-radius: 6px;">
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 4px;">{{ __('Description') }}</label>
                        <textarea name="description" id="planDescription" rows="2" style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-200); border-radius: 6px; resize: vertical;"></textarea>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 4px;">{{ __('Max Stores (-1 for Unlimited)') }}</label>
                            <input type="number" name="max_branches" id="planBranches" style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-200); border-radius: 6px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 4px;">{{ __('Max Users (-1 for Unlimited)') }}</label>
                            <input type="number" name="max_users" id="planUsers" style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-200); border-radius: 6px;">
                        </div>
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 8px;">{{ __('Features (JSON Format)') }}</label>
                        <textarea name="features" id="planFeatures" rows="5" style="width: 100%; padding: 8px 12px; border: 1px solid var(--gray-200); border-radius: 6px; font-family: monospace; font-size: 0.85rem; resize: vertical;"></textarea>
                        <p style="font-size: 0.75rem; color: var(--gray-400); margin-top: 4px;">{{ __('Enter features as a JSON array, e.g., ["product_management", "barcode_support"]') }}</p>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px;">
                        <button type="button" onclick="closeEditModal()" class="btn btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openEditModal(slug, name, description, price, branches, users, features) {
                document.getElementById('planName').value = name;
                document.getElementById('planDescription').value = description;
                document.getElementById('planPrice').value = price;
                document.getElementById('planBranches').value = branches;
                document.getElementById('planUsers').value = users;
                document.getElementById('planFeatures').value = JSON.stringify(features, null, 2);
                
                const form = document.getElementById('editPlanForm');
                form.action = `/pricing/${slug}`;
                
                document.getElementById('editPlanModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
            
            function closeEditModal() {
                document.getElementById('editPlanModal').style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        </script>
    @endif

    <div class="section animate-in" style="margin-top: 40px;">
        <h2 class="section-title"><i class="fas fa-info-circle"></i> {{ __('Need a Custom Plan?') }}</h2>
        <p style="color: var(--gray-500); margin-bottom: 20px;">
            {{ __('If your business has unique requirements or you manage a very large chain, contact our sales team for a custom enterprise solution.') }}
        </p>
        <a href="mailto:sales@poshere.lk" class="btn btn-secondary">
            <i class="fas fa-envelope"></i> {{ __('Contact Sales') }}
        </a>
    </div>
@endsection
