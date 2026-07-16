export const IMPACT_STATS = [
    { key: 'beneficiaries', end: 128000, decimals: 0, prefix: '', suffix: '+' },
    { key: 'campaigns', end: 24, decimals: 0, prefix: '', suffix: '' },
    { key: 'volunteers', end: 1450, decimals: 0, prefix: '', suffix: '+' },
    { key: 'donations', end: 3.2, decimals: 1, prefix: '$', suffix: 'M' },
];

export const CONTENT = {
    en: {
        nav: { home: 'Home', about: 'About', campaigns: 'Campaigns', updates: 'Updates', team: 'Our Team', donate: 'Donate Now' },
        hero: {
            badge: 'Humanitarian relief, rooted in dignity',
            title: 'Giving that grows. Impact that lasts.',
            subtitle: 'GHOSN Relief Team stands beside families and communities — turning everyday generosity into lasting change that protects human dignity.',
            ctaPrimary: 'Explore Campaigns',
            ctaSecondary: 'Join Our Team',
        },
        about: {
            eyebrow: 'About GHOSN',
            title: 'Who We Are',
            watch: 'Watch our story',
            readMore: 'Read more',
            paragraphs: [
                'GHOSN grew from a simple belief: generosity, given with care, takes root and keeps growing long after the moment of giving.',
                'We work hand in hand with local communities to deliver relief that is timely, transparent, and always respectful of human dignity.',
                'Our vision is a world where every act of giving becomes lasting hope — sustainable programs that empower families to stand on their own.',
            ],
            stats: [
                { value: '2018', label: 'Established' },
                { value: '1,450+', label: 'Volunteers' },
                { value: '60+', label: 'Communities' },
            ],
        },
        impact: { title: 'Our Impact in Numbers', labels: ['Beneficiaries reached', 'Active campaigns', 'Volunteers', 'Total donations'] },
        howWorks: {
            eyebrow: 'Simple & transparent',
            title: 'How your giving works',
            intro: 'Three steps from your heart to a family in need — with full transparency at every stage.',
            steps: [
                { title: 'Choose a cause', body: 'Pick a campaign that speaks to you, or give where the need is greatest.' },
                { title: 'Give securely', body: 'Donate in seconds through a safe, encrypted checkout.' },
                { title: 'See the impact', body: 'Follow real updates and watch your generosity grow into change.' },
            ],
        },
        ways: {
            eyebrow: 'Get involved',
            title: 'Ways you can help',
            intro: 'Every hand matters. Choose the way that fits you best.',
            cards: [
                { title: 'Donate', body: 'Fund clean water, food, and shelter for families in need.', cta: 'Give now' },
                { title: 'Volunteer', body: 'Lend your time and skills to relief work on the ground.', cta: 'Join us' },
                { title: 'Partner with us', body: 'Bring your organization on board to multiply the impact.', cta: 'Get in touch' },
            ],
        },
        testimonials: {
            eyebrow: 'Voices of hope',
            title: 'Stories from our community',
            items: [
                { quote: 'The new well changed everything. My children no longer walk hours for water — they walk to school instead.', name: 'Amina', role: 'Beneficiary, Northern Region' },
                { quote: 'Giving through GHOSN, I actually see where my donation goes. That transparency is why I keep coming back.', name: 'Khalid', role: 'Monthly donor' },
                { quote: 'Volunteering here gave my weekends meaning. We are a family bound by one purpose.', name: 'Layla', role: 'Field volunteer' },
            ],
        },
        newsletter: {
            title: 'Stay close to the impact',
            subtitle: 'Join our newsletter for stories, campaign milestones, and ways to help.',
            placeholder: 'Your email address',
            button: 'Subscribe',
            success: 'You are subscribed — thank you!',
            error: 'Something went wrong. Please try again.',
        },
        campaigns: {
            eyebrow: 'Where your giving goes',
            title: 'Current Campaigns',
            intro: 'Every campaign is a promise kept. Choose a cause and watch your support grow into real, measurable change.',
            tags: { urgent: 'Urgent', ongoing: 'Ongoing' },
            goalLabel: 'of',
            funded: 'funded',
            donate: 'Donate to this campaign',
            viewAll: 'View all campaigns',
            empty: 'Active campaigns will appear here soon.',
        },
        join: {
            eyebrow: 'Become a volunteer',
            title: 'Join Our Team',
            copy: 'Become part of a community that shows up when it matters. Your time, skills and heart create real change in people lives.',
            bullets: ['Flexible time commitment', 'Training and mentorship', 'Real, visible impact'],
            name: 'Full name',
            namePh: 'Your full name',
            phone: 'Phone number',
            phonePh: '+1 (555) 000-0000',
            email: 'Email',
            emailPh: 'you@email.com',
            areaLabel: 'Preferred volunteering area',
            areaPh: 'Select an area',
            areas: [
                { value: 'field_relief', label: 'Field relief' },
                { value: 'fundraising', label: 'Fundraising' },
                { value: 'media', label: 'Media and outreach' },
                { value: 'logistics', label: 'Logistics' },
                { value: 'education', label: 'Education and youth' },
            ],
            message: 'Short message',
            messagePh: 'Tell us how you would like to help',
            submit: 'Submit application',
            sending: 'Sending…',
            success: 'Thank you! We will reach out to you very soon.',
            error: 'Something went wrong. Please try again.',
            err: { required: 'This field is required', email: 'Enter a valid email address' },
        },
        blog: {
            eyebrow: 'Newsroom',
            title: 'Latest Updates & News',
            viewAll: 'View All Updates',
            readMore: 'Read more',
            empty: 'News and updates will appear here soon.',
        },
        contact: { title: 'Contact', phoneLabel: 'Phone', emailLabel: 'Email', addressLabel: 'Address', address: '14 Olive Grove Street, Community District' },
        footer: {
            desc: 'A humanitarian relief team turning everyday generosity into lasting, dignified change.',
            tagline: 'Giving that grows... an impact that lasts',
            quick: 'Quick Links',
            explore: 'Explore',
            follow: 'Follow us',
            rights: 'All rights reserved.',
        },
    },
    ar: {
        nav: { home: 'الرئيسية', about: 'من نحن', campaigns: 'الحملات', updates: 'المستجدات', team: 'فريقنا', donate: 'تبرّع الآن' },
        hero: {
            badge: 'إغاثة إنسانية تحفظ الكرامة',
            title: 'عطاءٌ ينمو... وأثرٌ يبقى',
            subtitle: 'فريق غُصن للإغاثة يقف إلى جانب الأسر والمجتمعات، ليحوّل العطاء اليومي إلى تغييرٍ دائمٍ يصون كرامة الإنسان.',
            ctaPrimary: 'استكشف الحملات',
            ctaSecondary: 'انضم إلى فريقنا',
        },
        about: {
            eyebrow: 'من نحن',
            title: 'قصة غُصن',
            watch: 'شاهد قصتنا',
            readMore: 'اقرأ المزيد',
            paragraphs: [
                'نشأ غُصن من إيمانٍ بسيط: أنّ العطاء حين يُقدَّم بعناية يتجذّر ويستمرّ في النمو بعد لحظة العطاء بزمنٍ طويل.',
                'نعمل يدًا بيد مع المجتمعات المحلية لتقديم إغاثةٍ سريعةٍ وشفّافة، تحفظ كرامة الإنسان دائمًا.',
                'رؤيتنا عالمٌ يتحوّل فيه كل عطاءٍ إلى أملٍ دائم — برامج مستدامة تُمكّن الأسر من الاعتماد على نفسها.',
            ],
            stats: [
                { value: '٢٠١٨', label: 'سنة التأسيس' },
                { value: '+١٤٥٠', label: 'متطوّع' },
                { value: '+٦٠', label: 'مجتمعًا' },
            ],
        },
        impact: { title: 'أثرنا بالأرقام', labels: ['المستفيدون', 'الحملات النشطة', 'المتطوعون', 'إجمالي التبرعات'] },
        howWorks: {
            eyebrow: 'بسيط وشفّاف',
            title: 'كيف يعمل عطاؤك',
            intro: 'ثلاث خطوات من قلبك إلى أسرةٍ محتاجة — بشفافيةٍ كاملة في كل مرحلة.',
            steps: [
                { title: 'اختر قضية', body: 'اختر حملةً تلامس قلبك، أو تبرّع حيث الحاجة أكبر.' },
                { title: 'تبرّع بأمان', body: 'تبرّع في ثوانٍ عبر بوّابة دفعٍ آمنة ومشفّرة.' },
                { title: 'شاهد الأثر', body: 'تابع التحديثات الحقيقية وشاهد عطاءك يتحوّل إلى تغيير.' },
            ],
        },
        ways: {
            eyebrow: 'شارك معنا',
            title: 'طرقٌ يمكنك أن تساعد بها',
            intro: 'كل يدٍ تصنع فرقًا. اختر الطريقة التي تناسبك.',
            cards: [
                { title: 'تبرّع', body: 'موّل المياه النظيفة والغذاء والمأوى للأسر المحتاجة.', cta: 'تبرّع الآن' },
                { title: 'تطوّع', body: 'امنح وقتك ومهاراتك للعمل الإغاثي في الميدان.', cta: 'انضم إلينا' },
                { title: 'كن شريكًا', body: 'أشرك مؤسستك معنا لمضاعفة الأثر.', cta: 'تواصل معنا' },
            ],
        },
        testimonials: {
            eyebrow: 'أصوات الأمل',
            title: 'قصصٌ من مجتمعنا',
            items: [
                { quote: 'البئر الجديد غيّر كل شيء. لم يعد أطفالي يمشون ساعاتٍ لأجل الماء — صاروا يمشون إلى المدرسة.', name: 'أمينة', role: 'مستفيدة، المنطقة الشمالية' },
                { quote: 'حين أتبرّع عبر غُصن أرى فعلًا أين يذهب عطائي. هذه الشفافية سبب عودتي دائمًا.', name: 'خالد', role: 'متبرّع شهري' },
                { quote: 'التطوّع هنا منح عطلاتي معنى. صرنا عائلةً يجمعها هدفٌ واحد.', name: 'ليلى', role: 'متطوّعة ميدانية' },
            ],
        },
        newsletter: {
            title: 'ابقَ قريبًا من الأثر',
            subtitle: 'اشترك في نشرتنا للقصص ومحطات الحملات وطرق المساعدة.',
            placeholder: 'بريدك الإلكتروني',
            button: 'اشترك',
            success: 'تم اشتراكك — شكرًا لك!',
            error: 'حدث خطأ. يرجى المحاولة مرة أخرى.',
        },
        campaigns: {
            eyebrow: 'إلى أين يذهب عطاؤك',
            title: 'الحملات الحالية',
            intro: 'كل حملةٍ وعدٌ نوفي به. اختر قضيةً وشاهد دعمك ينمو إلى أثرٍ حقيقيٍّ ملموس.',
            tags: { urgent: 'عاجلة', ongoing: 'مستمرة' },
            goalLabel: 'من',
            funded: 'مموّل',
            donate: 'تبرّع لهذه الحملة',
            viewAll: 'عرض كل الحملات',
            empty: 'ستظهر الحملات النشطة هنا قريبًا.',
        },
        join: {
            eyebrow: 'كن متطوعًا',
            title: 'انضم إلى فريقنا',
            copy: 'كن جزءًا من مجتمعٍ يحضر عند الحاجة. وقتك ومهاراتك وقلبك تصنع فرقًا حقيقيًا في حياة الناس.',
            bullets: ['مرونة في الوقت', 'تدريب وإرشاد', 'أثرٌ ملموس وواضح'],
            name: 'الاسم الكامل',
            namePh: 'اكتب اسمك الكامل',
            phone: 'رقم الهاتف',
            phonePh: '+٩٦٦ ٥٠ ٠٠٠ ٠٠٠٠',
            email: 'البريد الإلكتروني',
            emailPh: 'you@email.com',
            areaLabel: 'مجال التطوّع المفضّل',
            areaPh: 'اختر مجالاً',
            areas: [
                { value: 'field_relief', label: 'الإغاثة الميدانية' },
                { value: 'fundraising', label: 'جمع التبرعات' },
                { value: 'media', label: 'الإعلام والتواصل' },
                { value: 'logistics', label: 'اللوجستيات' },
                { value: 'education', label: 'التعليم والشباب' },
            ],
            message: 'رسالة قصيرة',
            messagePh: 'أخبرنا كيف تودّ المساعدة',
            submit: 'أرسل الطلب',
            sending: 'جارٍ الإرسال…',
            success: 'شكرًا لك! سنتواصل معك قريبًا جدًا.',
            error: 'حدث خطأ ما. حاول مرة أخرى.',
            err: { required: 'هذا الحقل مطلوب', email: 'أدخل بريدًا إلكترونيًا صحيحًا' },
        },
        blog: {
            eyebrow: 'غرفة الأخبار',
            title: 'آخر الأخبار والمستجدات',
            viewAll: 'عرض كل المستجدات',
            readMore: 'اقرأ المزيد',
            empty: 'ستظهر الأخبار والمستجدات هنا قريبًا.',
        },
        contact: { title: 'تواصل', phoneLabel: 'الهاتف', emailLabel: 'البريد', addressLabel: 'العنوان', address: 'شارع بستان الزيتون ١٤، حي المجتمع' },
        footer: {
            desc: 'فريق إغاثة إنساني يحوّل العطاء اليومي إلى تغييرٍ دائمٍ يحفظ الكرامة.',
            tagline: 'عطاءٌ ينمو... وأثرٌ يبقى',
            quick: 'روابط سريعة',
            explore: 'استكشف',
            follow: 'تابعنا',
            rights: 'جميع الحقوق محفوظة.',
        },
    },
};

export function getBootstrap() {
    return window.__GHOSN_LANDING__ ?? {};
}

export function formatMoney(amount, currency = 'USD') {
    try {
        return new Intl.NumberFormat('en-US', { style: 'currency', currency, maximumFractionDigits: 0 }).format(amount);
    } catch {
        return `$${Math.round(amount).toLocaleString('en-US')}`;
    }
}

export function pickLocalized(item, lang, field) {
    const key = `${field}_${lang}`;

    return item?.[key] ?? item?.[`${field}_en`] ?? '';
}
