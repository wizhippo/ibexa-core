<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\User\RoleCreateStruct as APIRoleCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct as APIPolicyCreateStruct;

/**
 * This class is used to create a new role.
 *
 * @internal Meant for internal use by Repository, type hint against API instead.
 */
class RoleCreateStruct extends APIRoleCreateStruct
{
    /**
     * Policies associated with the role.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct[]
     */
    protected $policies = [];

    /**
     * Returns policies associated with the role.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct[]
     */
    public function getPolicies(): iterable
    {
        return $this->policies;
    }

    /**
     * Adds a policy to this role.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct $policyCreateStruct
     */
    public function addPolicy(APIPolicyCreateStruct $policyCreateStruct): void
    {
        $this->policies[] = $policyCreateStruct;
    }
}

class_alias(RoleCreateStruct::class, 'eZ\Publish\Core\Repository\Values\User\RoleCreateStruct');