<?php

use TimoDeWinter\FilamentAuthorization\FilamentAuthorization;

beforeEach(function () {
    $this->authorization = new FilamentAuthorization;
});

describe('Permission registration', function () {
    it('registers a flat list of permissions', function () {
        $this->authorization->registerPermission(['view', 'create'], 'invoices', 'Invoices');

        expect($this->authorization->getPermissions('Default', 'invoices'))->toBe(['view', 'create'])
            ->and($this->authorization->getPrefixTranslation('invoices'))->toBe('Invoices');
    });

    it('registers keyed permissions with labels', function () {
        $this->authorization->registerPermission(['create' => 'Create invoices'], 'invoices', 'Invoices');

        expect($this->authorization->getPermissions('Default', 'invoices'))->toBe(['create' => 'Create invoices']);
    });

    it('merges repeat registrations for the same prefix', function () {
        $this->authorization->registerPermission(['create' => 'Create'], 'invoices', 'Invoices');
        $this->authorization->registerPermission(['delete' => 'Delete'], 'invoices', 'Invoices');

        expect($this->authorization->getPermissions('Default', 'invoices'))
            ->toBe(['create' => 'Create', 'delete' => 'Delete']);
    });

    it('appends rather than overwrites when repeat registrations use integer keys', function () {
        $this->authorization->registerPermission(['view'], 'invoices', 'Invoices');
        $this->authorization->registerPermission(['create'], 'invoices', 'Invoices');

        expect($this->authorization->getPermissions('Default', 'invoices'))->toBe(['view', 'create']);
    });

    it('groups permissions under the given tab', function () {
        $this->authorization->registerPermission(['view'], 'invoices', 'Invoices', 'Finance');

        expect($this->authorization->getTabs())->toBe(['Finance'])
            ->and($this->authorization->getPrefixGroups('Finance'))->toBe(['invoices']);
    });
});

describe('Permission descriptions', function () {
    it('unwraps the array shape into a label plus a description', function () {
        $this->authorization->registerPermission(
            permission: [
                'create' => [
                    'label' => 'Create invoices',
                    'description' => 'Also allows saving drafts',
                ],
            ],
            prefix: 'invoices',
            prefixTranslation: 'Invoices',
        );

        expect($this->authorization->getPermissions('Default', 'invoices'))->toBe(['create' => 'Create invoices'])
            ->and($this->authorization->getDescription('invoices', 'create'))->toBe('Also allows saving drafts');
    });

    it('falls back to the key when the array shape omits a label', function () {
        $this->authorization->registerPermission(
            permission: ['create' => ['description' => 'Some help text']],
            prefix: 'invoices',
            prefixTranslation: 'Invoices',
        );

        expect($this->authorization->getPermissions('Default', 'invoices'))->toBe(['create' => 'create']);
    });

    it('returns null for a permission registered without a description', function () {
        $this->authorization->registerPermission(['create' => 'Create'], 'invoices', 'Invoices');

        expect($this->authorization->getDescription('invoices', 'create'))->toBeNull();
    });

    it('records the group description', function () {
        $this->authorization->registerPermission(
            permission: ['create' => 'Create'],
            prefix: 'invoices',
            prefixTranslation: 'Invoices',
            prefixDescription: 'Everything invoice related',
        );

        expect($this->authorization->getPrefixDescription('invoices'))->toBe('Everything invoice related');
    });

    it('returns null for a group registered without a description', function () {
        $this->authorization->registerPermission(['create' => 'Create'], 'invoices', 'Invoices');

        expect($this->authorization->getPrefixDescription('invoices'))->toBeNull();
    });

    it('keeps an earlier group description when a later registration omits one', function () {
        $this->authorization->registerPermission(
            permission: ['create' => 'Create'],
            prefix: 'invoices',
            prefixTranslation: 'Invoices',
            prefixDescription: 'Everything invoice related',
        );
        $this->authorization->registerPermission(['delete' => 'Delete'], 'invoices', 'Invoices');

        expect($this->authorization->getPrefixDescription('invoices'))->toBe('Everything invoice related');
    });
});

describe('Tab ordering', function () {
    it('keeps registration order when no order is set', function () {
        $this->authorization->registerPermission(['view'], 'invoices', 'Invoices', 'Finance');
        $this->authorization->registerPermission(['view'], 'absences', 'Absences', 'HRM');

        expect($this->authorization->getTabs())->toBe(['Finance', 'HRM']);
    });

    it('pins the tabs listed in the order', function () {
        $this->authorization->registerPermission(['view'], 'invoices', 'Invoices', 'Finance');
        $this->authorization->registerPermission(['view'], 'absences', 'Absences', 'HRM');

        $this->authorization->setTabOrder(['HRM', 'Finance']);

        expect($this->authorization->getTabs())->toBe(['HRM', 'Finance']);
    });

    it('appends registered tabs that the order does not mention', function () {
        $this->authorization->registerPermission(['view'], 'invoices', 'Invoices', 'Finance');
        $this->authorization->registerPermission(['view'], 'absences', 'Absences', 'HRM');
        $this->authorization->registerPermission(['view'], 'projects', 'Projects', 'Projects');

        $this->authorization->setTabOrder(['Projects']);

        expect($this->authorization->getTabs())->toBe(['Projects', 'Finance', 'HRM']);
    });

    it('ignores ordered tabs that were never registered', function () {
        $this->authorization->registerPermission(['view'], 'invoices', 'Invoices', 'Finance');

        $this->authorization->setTabOrder(['Nonexistent', 'Finance']);

        expect($this->authorization->getTabs())->toBe(['Finance']);
    });
});
