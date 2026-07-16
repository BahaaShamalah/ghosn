<?php

/**
 * Default volunteer / join-us email templates (admin-editable via Settings → Volunteers).
 *
 * Placeholders: {name}, {email}, {phone}, {area}, {area_label}, {message}, {site_name}
 */
return [
    'confirmation' => [
        'enabled' => true,
        'subject_en' => 'We received your volunteer application — GHOSN',
        'subject_ar' => 'استلمنا طلب التطوّع — غُصن',
        'heading_en' => 'Thank you for applying',
        'heading_ar' => 'شكرًا لتقديمك',
        'body_en' => "Dear {name},\n\nWe have received your application to volunteer with {site_name}. Our coordinator will review your details and reach out soon.\n\nPreferred area: {area_label}",
        'body_ar' => "عزيزي/عزيزتي {name}،\n\nاستلمنا طلبك للتطوّع مع {site_name}. سيراجع منسّق المتطوعين بياناتك ويتواصل معك قريبًا.\n\nالمجال المفضّل: {area_label}",
    ],
    'admin_alert' => [
        'enabled' => true,
        'subject_en' => 'New volunteer application — {name}',
        'subject_ar' => 'طلب تطوّع جديد — {name}',
        'heading_en' => 'New volunteer application',
        'heading_ar' => 'طلب تطوّع جديد',
        'body_en' => "A new volunteer application was submitted.\n\nName: {name}\nEmail: {email}\nPhone: {phone}\nArea: {area_label}\n\nMessage:\n{message}",
        'body_ar' => "تم تقديم طلب تطوّع جديد.\n\nالاسم: {name}\nالبريد: {email}\nالهاتف: {phone}\nالمجال: {area_label}\n\nالرسالة:\n{message}",
    ],
    'welcome' => [
        'enabled' => true,
        'subject_en' => 'Welcome to the GHOSN volunteer family',
        'subject_ar' => 'أهلًا بك في عائلة متطوّعي غُصن',
        'heading_en' => 'Welcome to the team!',
        'heading_ar' => 'أهلًا بك في الفريق!',
        'body_en' => "Dear {name},\n\nWe are delighted to welcome you to {site_name}. Someone from our volunteer team will contact you shortly with next steps.\n\nThank you for stepping up when it matters.",
        'body_ar' => "عزيزي/عزيزتي {name}،\n\nيسعدنا أن نرحّب بك في {site_name}. سيتواصل معك أحد أعضاء فريق المتطوعين قريبًا بخطوات الانضمام.\n\nشكرًا لحضورك عند الحاجة.",
    ],
    'rejected' => [
        'enabled' => true,
        'subject_en' => 'Update on your GHOSN volunteer application',
        'subject_ar' => 'تحديث بخصوص طلب التطوّع مع غُصن',
        'heading_en' => 'Thank you for your interest',
        'heading_ar' => 'شكرًا لاهتمامك',
        'body_en' => "Dear {name},\n\nThank you for offering your time with {site_name}. We are unable to move forward with your application at this time, but we truly appreciate your willingness to help.",
        'body_ar' => "عزيزي/عزيزتي {name}،\n\nشكرًا لعرضك وقتك مع {site_name}. لا يمكننا المتابعة مع طلبك حاليًا، لكننا نقدّر رغبتك في المساعدة.",
    ],
];
