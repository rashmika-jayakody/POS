<?php

return [
    'essential' => [
        'name' => 'Essential',
        'price_lkr' => 35000,
        'price_display' => 'LKR 350/month',
        'payhere_recurrence' => '1 Month',
        'tagline' => 'Perfect for startups & small stores',
        'features' => [
            'Up to 2,000 products',
            'Basic billing & sales',
            'Customer management',
            'Daily sales reports',
            'Email support',
        ],
        'excluded' => [
            'Inventory alerts',
            'Multi-store sync',
        ],
    ],
    'professional' => [
        'name' => 'Professional',
        'price_lkr' => 85000,
        'price_display' => 'LKR 850/month',
        'payhere_recurrence' => '1 Month',
        'tagline' => 'For growing businesses',
        'features' => [
            'Up to 10,000 products',
            'Advanced billing features',
            'Smart inventory management',
            'Supplier & vendor tracking',
            'GST & invoice generation',
            'Low stock alerts',
            'Priority phone support',
        ],
        'excluded' => [],
    ],
    'enterprise' => [
        'name' => 'Enterprise',
        'price_lkr' => 175000,
        'price_display' => 'LKR 1,750/month',
        'payhere_recurrence' => '1 Month',
        'tagline' => 'For retail chains & large stores',
        'features' => [
            'Unlimited products',
            'Multi-store management',
            'Advanced AI analytics',
            'Custom loyalty programs',
            'Full API access',
            'Custom feature development',
            '24/7 dedicated support',
        ],
        'excluded' => [],
    ],
];