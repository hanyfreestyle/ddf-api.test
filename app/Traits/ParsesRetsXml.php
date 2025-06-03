<?php

namespace App\Traits;

trait ParsesRetsXml {
    /**
     * يحوِّل نص XML القادم من DDF® إلى مصفوفة PHP.
     *
     * - يزيل ترويسة <RETS> و<RETS-RESPONSE>
     * - يحوِّل العناصر إلى مصفوفة عادية مع مراعاة العناصر المتعددة
     */
    protected function xmlToArray(string $xml): array {
        // حمِّل XML مع تعطيل الـ DTD لاعتبارات الأمان
        $simple = simplexml_load_string(
            $xml,
            'SimpleXMLElement',
            LIBXML_NOCDATA | LIBXML_NONET
        );

        // بعض الاستجابات ملفوفة داخل <RETS><RETS-RESPONSE>…
        // ننزل لأول عقدة فعلية إذا وُجدت
        if (
            isset($simple->{"RETS-RESPONSE"}) &&
            $simple->{"RETS-RESPONSE"}->count()
        ) {
            $simple = $simple->{"RETS-RESPONSE"}->children();
        }

        // حوِّل إلى JSON ثم Array للحفاظ على البنية
        return json_decode(json_encode($simple), true) ?? [];
    }
}
