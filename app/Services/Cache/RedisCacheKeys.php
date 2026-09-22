<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RedisCacheKeys
{
    // Key names and prefixes
    public const TOPICS = 'vocabulary:topics';
    public const LESSON_CONTENT_PREFIX = 'vocabulary:lesson:';
    public const LESSONS_VERSION = 'vocabulary:lessons:version';
    public const PROMPTS_VERSION = 'writing:prompts:version';
    public const PROMPT_PREFIX = 'writing:prompt:';
    public const STUDENT_DASHBOARD_PREFIX = 'student:dashboard:';
    public const PRACTICE_ACTIVE_PREFIX = 'practice:active:';
    public const SCORE_LOCK_PREFIX = 'writing:score:';

    // TTL in seconds
    public const TTL_TOPICS = 7200;            // 2 hours
    public const TTL_LESSON_CONTENT = 7200;    // 2 hours
    public const TTL_LESSONS = 7200;           // 2 hours
    public const TTL_PROMPTS = 7200;           // 2 hours
    public const TTL_PROMPT = 7200;            // 2 hours
    public const TTL_STUDENT_DASHBOARD = 300;  // 5 minutes
    public const TTL_PRACTICE_ACTIVE = 1800;   // 30 minutes
    public const TTL_SCORE_LOCK = 120;         // 2 minutes

    /**
     * Safely remember a cached value. If Redis is unavailable, gracefully fall back to the callback.
     *
     * @template T
     * @param \Closure(): T $callback
     * @return T
     */
    public static function rememberOrFallback(string $key, int $ttl, \Closure $callback): mixed
    {
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Throwable $e) {
            Log::warning("Redis cache error on key [{$key}], falling back to database: " . $e->getMessage());
            return $callback();
        }
    }

    /**
     * Build lesson content cache key.
     */
    public static function lessonContentKey(int $lessonId): string
    {
        return self::LESSON_CONTENT_PREFIX . "{$lessonId}:content";
    }

    /**
     * Build lessons paginated list cache key with version namespace.
     */
    public static function lessonsListKey(int $page, array $filters = []): string
    {
        $version = self::getLessonsVersion();
        $hash = !empty($filters) ? md5(json_encode($filters)) : 'all';
        return "vocabulary:lessons:v{$version}:p{$page}:{$hash}";
    }

    /**
     * Build writing prompt detail cache key.
     */
    public static function promptKey(int $promptId): string
    {
        return self::PROMPT_PREFIX . $promptId;
    }

    /**
     * Build writing prompts paginated list cache key with version namespace.
     */
    public static function promptsListKey(int $page, array $filters = []): string
    {
        $version = self::getPromptsVersion();
        $hash = !empty($filters) ? md5(json_encode($filters)) : 'all';
        return "writing:prompts:v{$version}:p{$page}:{$hash}";
    }

    /**
     * Build student dashboard cache key.
     */
    public static function studentDashboardKey(int $userId): string
    {
        return self::STUDENT_DASHBOARD_PREFIX . $userId;
    }

    /**
     * Build active practice session key.
     */
    public static function practiceActiveKey(int $userId, int $lessonId): string
    {
        return self::PRACTICE_ACTIVE_PREFIX . "{$userId}:{$lessonId}";
    }

    /**
     * Build score lock key.
     */
    public static function scoreLockKey(int $submissionId): string
    {
        return self::SCORE_LOCK_PREFIX . $submissionId;
    }

    /**
     * Get or initialize lessons list cache version.
     */
    public static function getLessonsVersion(): int
    {
        try {
            return (int) Cache::get(self::LESSONS_VERSION, 1);
        } catch (\Throwable) {
            return 1;
        }
    }

    /**
     * Get or initialize writing prompts list cache version.
     */
    public static function getPromptsVersion(): int
    {
        try {
            return (int) Cache::get(self::PROMPTS_VERSION, 1);
        } catch (\Throwable) {
            return 1;
        }
    }

    /**
     * Invalidate topics cache.
     */
    public static function invalidateTopics(): void
    {
        try {
            Cache::forget(self::TOPICS);
        } catch (\Throwable $e) {
            Log::warning("Failed to invalidate topics cache: " . $e->getMessage());
        }
    }

    /**
     * Invalidate lesson content and bump lessons list version.
     */
    public static function invalidateLesson(int $lessonId): void
    {
        try {
            Cache::forget(self::lessonContentKey($lessonId));
            if (! Cache::has(self::LESSONS_VERSION)) {
                Cache::put(self::LESSONS_VERSION, 2, 86400 * 30);
            } else {
                Cache::increment(self::LESSONS_VERSION);
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to invalidate lesson [{$lessonId}] cache: " . $e->getMessage());
        }
    }

    /**
     * Invalidate all lessons list cache by bumping version.
     */
    public static function invalidateLessonsList(): void
    {
        try {
            if (! Cache::has(self::LESSONS_VERSION)) {
                Cache::put(self::LESSONS_VERSION, 2, 86400 * 30);
            } else {
                Cache::increment(self::LESSONS_VERSION);
            }
        } catch (\Throwable) {
            Log::warning("Failed to invalidate lessons list cache: " . $e->getMessage());
        }
    }

    /**
     * Invalidate prompt detail and bump prompts list version.
     */
    public static function invalidatePrompt(int $promptId): void
    {
        try {
            Cache::forget(self::promptKey($promptId));
            if (! Cache::has(self::PROMPTS_VERSION)) {
                Cache::put(self::PROMPTS_VERSION, 2, 86400 * 30);
            } else {
                Cache::increment(self::PROMPTS_VERSION);
            }
        } catch (\Throwable) {
            Log::warning("Failed to invalidate prompt [{$promptId}] cache: " . $e->getMessage());
        }
    }

    /**
     * Invalidate student dashboard cache.
     */
    public static function invalidateStudentDashboard(int $userId): void
    {
        try {
            Cache::forget(self::studentDashboardKey($userId));
        } catch (\Throwable) {
            Log::warning("Failed to invalidate student dashboard [{$userId}] cache: " . $e->getMessage());
        }
    }

    /**
     * Invalidate active practice cache.
     */
    public static function invalidateActivePractice(int $userId, int $lessonId): void
    {
        try {
            Cache::forget(self::practiceActiveKey($userId, $lessonId));
        } catch (\Throwable) {
            Log::warning("Failed to invalidate active practice [{$userId}:{$lessonId}] cache: " . $e->getMessage());
        }
    }
}
