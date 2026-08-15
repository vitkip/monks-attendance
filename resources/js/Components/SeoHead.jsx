import { Head, usePage } from '@inertiajs/react';

export default function SeoHead({
    title,
    description,
    keywords,
    image,
    type = 'website',
    schemaJson = null,
}) {
    const { contact, appLogo, url: currentUrl } = usePage().props;
    const siteName = 'ວັດປ່າໜອງບົວທອງໃຕ້';
    
    const seoTitle = title ? `${title} — ${siteName}` : `${siteName} — ພຣະສົງ, ພຸດທະສາສະໜາ, ການປະຕິບັດທຳ, ກຳມະຖານ, ບວດຂາວ, ໄຫວ້ພຣະ`;
    
    const defaultDescription = 'ເວັບໄຊທາງການ ວັດປ່າໜອງບົວທອງໃຕ້ ເມືອງສີໂຄດຕະບອງ ນະຄອນຫຼວງວຽງຈັນ ປະເທດລາວ — ຂ່າວສານ, ຂໍ້ມູນພຣະສົງ ແລະ ສາມະເນນ, ບວດຂາວ, ການປະຕິບັດທຳ, ກຳມະຖານ, ບຸນປະເພນີ, ບົດສູດມົນໄຫວ້ພຣະ ແລະ ໂຄງການກໍ່ສ້າງພາຍໃນວັດ';
    const seoDescription = description || defaultDescription;
    
    const defaultKeywords = [
        'ວັດປ່າໜອງບົວທອງໃຕ້',
        'ວັດປ່າໜອງບົວທອງ',
        'ວັດໜອງບົວທອງ',
        'ພຣະສົງ',
        'ສາມະເນນ',
        'ແມ່ຂາວ',
        'ບວດຂາວ',
        'ໜອງບົວທອງ',
        'ບຸນ',
        'ບຸນປະເພນີ',
        'ພຸດທະສາສະໜາ',
        'ພຣະພຸດທະສາສະໜາ',
        'ປະເທດລາວ',
        'ການປະຕິບັດທຳ',
        'ກຳມະຖານ',
        'ນັ່ງສະມາທິ',
        'ໄຫວ້ພຣະ',
        'ບົດສູດມົນ',
        'ທຳມະ',
        'ວັດລາວ',
        'ເມືອງສີໂຄດຕະບອງ',
        'ນະຄອນຫຼວງວຽງຈັນ',
        'Wat Pa Nongbuathong Tai',
        'Wat Pa Nong Buathong',
        'Nongbuathong',
        'Monks Laos',
        'Buddhism Laos',
        'Dhamma Laos',
        'Kammathana Meditation',
        'Vientiane Temple'
    ].join(', ');
    
    const seoKeywords = keywords || defaultKeywords;
    const seoImage = image || (appLogo ? `/storage/${appLogo}` : '/favicon-512x512.png');
    const seoUrl = typeof window !== 'undefined' ? window.location.href : (currentUrl || '');
    const origin = typeof window !== 'undefined' ? window.location.origin : '';
    const fullImageUrl = seoImage.startsWith('http')
        ? seoImage
        : `${origin}${seoImage.startsWith('/') ? '' : '/'}${seoImage}`;

    const sameAs = [contact?.facebook, contact?.youtube].filter(Boolean);

    const defaultLdJson = schemaJson || {
        '@context': 'https://schema.org',
        '@graph': [
            {
                '@type': 'BuddhistTemple',
                '@id': `${origin}/#temple`,
                name: 'ວັດປ່າໜອງບົວທອງໃຕ້',
                alternateName: ['Wat Pa Nongbuathong Tai', 'ວັດປ່າໜອງບົວທອງ', 'ວັດໜອງບົວທອງໃຕ້'],
                description: seoDescription,
                url: origin,
                logo: fullImageUrl,
                image: fullImageUrl,
                address: {
                    '@type': 'PostalAddress',
                    streetAddress: 'ບ້ານ ໜອງບົວທອງໃຕ້',
                    addressLocality: 'ເມືອງ ສີໂຄດຕະບອງ',
                    addressRegion: 'ນະຄອນຫຼວງວຽງຈັນ',
                    addressCountry: 'LA',
                },
                ...(contact?.email ? { email: contact.email } : {}),
                ...(contact?.whatsapp ? { telephone: contact.whatsapp } : {}),
                ...(sameAs.length ? { sameAs } : {}),
                keywords: seoKeywords,
            },
            {
                '@type': 'WebSite',
                '@id': `${origin}/#website`,
                url: origin,
                name: siteName,
                description: seoDescription,
                inLanguage: 'lo',
            }
        ]
    };

    return (
        <Head title={seoTitle}>
            <meta name="description" content={seoDescription} />
            <meta name="keywords" content={seoKeywords} />
            <meta name="author" content="ວັດປ່າໜອງບົວທອງໃຕ້" />
            <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
            <link rel="canonical" href={seoUrl} />

            {/* Open Graph / Facebook */}
            <meta property="og:type" content={type} />
            <meta property="og:site_name" content={siteName} />
            <meta property="og:locale" content="lo_LA" />
            <meta property="og:title" content={seoTitle} />
            <meta property="og:description" content={seoDescription} />
            <meta property="og:url" content={seoUrl} />
            <meta property="og:image" content={fullImageUrl} />

            {/* Twitter */}
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" content={seoTitle} />
            <meta name="twitter:description" content={seoDescription} />
            <meta name="twitter:image" content={fullImageUrl} />

            {/* Structured Data (JSON-LD) */}
            <script type="application/ld+json">{JSON.stringify(defaultLdJson)}</script>
        </Head>
    );
}
