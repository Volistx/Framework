<?php

namespace App\ValidationRules\Auth;

use App\Facades\Messages;
use App\ValidationRules\ValidationRuleBase;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitValidationRule extends ValidationRuleBase
{
    public function Validate(): bool|array
    {
        $token = $this->inputs['token'];
        $plan = $this->inputs['plan'];

        if(isset($plan['RPM'])){
            $executed = RateLimiter::attempt(
                $token->subscription_id, $plan['RPM'],
                function () {
                }
            );

            if (!$executed) {
                return [
                    'message' => Messages::E429(),
                    'code' => 429
                ];
            }
        }
        return true;
    }
}