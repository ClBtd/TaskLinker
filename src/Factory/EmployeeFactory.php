<?php

namespace App\Factory;

use App\Entity\Employee;
use App\Enum\EmployeeStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Employee>
 */
final class EmployeeFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }
    
    public static function class(): string
    {
        return Employee::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'firstName' => self::faker()->firstName($gender = 'male'|'female'),
            'lastName' => self::faker()->unique()->lastName(),
            'startedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeThisDecade()),
            'status' => self::faker()->randomElement(EmployeeStatus::cases()),
            'roles' => ['ROLE_USER'],
            'password' => password_hash('password', PASSWORD_BCRYPT)
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
        ->afterInstantiate(function(Employee $employee): void {
            $employee->setEmail(strtolower($employee->getFirstName() . '.' . $employee->getLastName()) . '@example.com');
        });
    }
}
