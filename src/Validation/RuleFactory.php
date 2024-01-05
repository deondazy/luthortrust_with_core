<?php

declare(strict_types=1);

namespace Denosys\Core\Validation;

use Denosys\Core\Validation\Rules\StandardRule;
use Denosys\Core\Validation\Rules\RuleInterface;
use Denosys\Core\Validation\Rules\ValidationRuleException;

class RuleFactory
{
    private static array $customRules = [];

    public static function register(string $ruleName, callable $constructor): void
    {
        self::$customRules[$ruleName] = $constructor;
    }

    public static function make(string $rule, array $data = []): RuleInterface
    {
        [$ruleName, $parameter] = explode(':', $rule . ':');
        
        if (strpos($rule, ':') === false) {
            return new StandardRule();
        }

        if (array_key_exists($ruleName, self::$customRules)) {
            return call_user_func(self::$customRules[$ruleName], $parameter, $data);
        }

        throw new ValidationRuleException("No rule found for: $ruleName");
    }
}
