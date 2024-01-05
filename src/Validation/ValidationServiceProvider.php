<?php

declare(strict_types=1);

namespace Denosys\Core\Validation;

use Doctrine\ORM\EntityManagerInterface;
use Denosys\Core\Support\ServiceProvider;
use Denosys\Core\Validation\Rules\MaxRule;
use Denosys\Core\Validation\Rules\MinRule;
use Denosys\Core\Validation\Rules\UniqueRule;
use Denosys\Core\Validation\Rules\StandardRule;
use Denosys\Core\Validation\Rules\DateFormatRule;

class ValidationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->set(ValidatorInterface::class, function () {
            return new Validator();
        });

        $this->registerRules();
    }

    private function registerRules()
    {
        RuleFactory::register('required', fn () => new StandardRule());
        RuleFactory::register('min', fn ($parameter) => new MinRule($parameter));
        RuleFactory::register('max', fn ($parameter) => new MaxRule($parameter));
        RuleFactory::register('dateFormat', fn ($parameter) => new DateFormatRule($parameter));
        RuleFactory::register('unique', fn ($tableName, $data) => new UniqueRule(
            $this->container->get(EntityManagerInterface::class),
            $tableName,
            $data
        ));
    }
}
