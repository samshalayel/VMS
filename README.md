# VMS - ClickUp Integration

نظام إدارة الموردين مع تكامل ClickUp API

## المتطلبات

- PHP >= 7.4
- Composer
- حساب ClickUp مع API Token

## التثبيت

1. تثبيت المكتبات المطلوبة:
```bash
composer install
```

2. إعداد ملف البيئة:
```bash
copy .env.example .env
```

3. تحديث ملف `.env` بمعلومات ClickUp الخاصة بك:
   - `CLICKUP_API_TOKEN`: يمكن الحصول عليه من https://app.clickup.com/settings/apps
   - `CLICKUP_TEAM_ID`: معرف الفريق (Workspace)
   - `CLICKUP_SPACE_ID`: معرف المساحة
   - `CLICKUP_LIST_ID`: معرف القائمة

## الاستخدام

### مثال أساسي

```php
<?php
require_once 'vendor/autoload.php';

use VMS\ClickUpClient;

$clickup = new ClickUpClient('your_api_token');

// إنشاء مهمة جديدة
$task = $clickup->createTask('list_id', [
    'name' => 'مهمة جديدة',
    'description' => 'وصف المهمة',
    'status' => 'to do',
    'priority' => 3
]);

// الحصول على جميع المهام
$tasks = $clickup->getTasks('list_id');

// تحديث مهمة
$updated = $clickup->updateTask('task_id', [
    'name' => 'مهمة محدثة',
    'status' => 'in progress'
]);

// إضافة تعليق
$comment = $clickup->addComment('task_id', 'تعليق جديد');
```

### تشغيل المثال

```bash
php example.php
```

## الميزات المتاحة

### إدارة المهام
- ✅ إنشاء مهمة جديدة
- ✅ الحصول على المهام
- ✅ تحديث مهمة
- ✅ حذف مهمة
- ✅ إضافة تعليقات

### إدارة المساحات والقوائم
- ✅ الحصول على الفرق (Teams)
- ✅ الحصول على المساحات (Spaces)
- ✅ الحصول على القوائم (Lists)

### Webhooks
- ✅ إنشاء webhook
- ✅ الحصول على webhooks
- ✅ معالج webhook جاهز

### الحقول المخصصة
- ✅ تعيين قيم للحقول المخصصة

## Webhooks

لاستخدام Webhooks:

1. تأكد من أن ملف `webhook.php` متاح عبر URL عام
2. قم بإنشاء webhook:

```php
$webhook = $clickup->createWebhook($teamId, 'https://yourdomain.com/webhook.php', [
    'taskCreated',
    'taskUpdated',
    'taskDeleted',
    'taskCommentPosted',
    'taskStatusUpdated'
]);
```

3. سيتم تسجيل جميع الأحداث في `logs/webhook.log`

## الأحداث المدعومة

- `taskCreated` - إنشاء مهمة جديدة
- `taskUpdated` - تحديث مهمة
- `taskDeleted` - حذف مهمة
- `taskCommentPosted` - إضافة تعليق
- `taskStatusUpdated` - تغيير حالة المهمة
- `taskAssigneeUpdated` - تغيير المسؤول عن المهمة

## الأمان

- لا تشارك API Token الخاص بك
- استخدم HTTPS للـ webhooks
- راجع سجلات الأحداث بانتظام

## الوثائق الرسمية

للمزيد من المعلومات: https://clickup.com/api

## الدعم

لأي استفسارات أو مشاكل، يرجى فتح issue في المستودع.
