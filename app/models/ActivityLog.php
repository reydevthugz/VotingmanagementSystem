<?php
namespace App\Models;

class ActivityLog
{
    public function record(int $userId, string $action, array $context = [], string $ipAddress = '', string $userAgent = ''): void
    {
        MockStorage::addActivityLog([
            'log_id' => time() . rand(1, 999),
            'user_id' => $userId,
            'action' => $action,
            'context' => json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
