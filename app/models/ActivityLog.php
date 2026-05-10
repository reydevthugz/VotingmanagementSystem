<?php
namespace App\Models;

class ActivityLog extends BaseModel
{
    public function record(int $userId, string $action, array $context = [], string $ipAddress = '', string $userAgent = ''): void
    {
        $sql = 'INSERT INTO activity_logs (user_id, action, context, ip_address, user_agent, created_at)
                VALUES (:user_id, :action, :context, :ip_address, :user_agent, NOW())';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'context' => json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
