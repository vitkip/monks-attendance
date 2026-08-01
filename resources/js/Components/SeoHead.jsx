import { Head, usePage } from '@inertiajs/react';

export default function SeoHead({ title, description, image }) {
    const { contact, appLogo, url: currentUrl } = usePage().props;
    const siteName = document.querySelector('meta[name="app-name"]')?.content || 'ວັດປ່າໜອງບົວທອງໃຕ້';
    const seoTitle = title ? `${title} — ${siteName}` : siteName;
    const seoDescription = description || 'ວັດປ່າໜອງບົວທອງໃຕ້ — ຂ່າວສານ, ຂໍ້ມູນພຣະສົງ ແລະ ສາມະເນນ, ບົດສູດມົນ, ແລະ ໂຄງການກໍ່ສ້າງພາຍໃນວັດ';
    const seoImage = image || (appLogo ? `/storage/${appLogo}` : '/favicon-512x512.png');
    const seoUrl = typeof window !== 'undefined' ? window.location.href : currentUrl;

    const sameAs = [contact?.facebook, contact?.youtube].filter(Boolean);
    const ldJson = {
        '@context': 'https://schema.org',
        '@type': 'Organization',
        name: siteName,
        url: typeof window !== 'undefined' ? window.location.origin : '',
        logo: seoImage,
        ...(contact?.email ? { email: contact.email } : {}),
        ...(contact?.whatsapp ? { telephone: contact.whatsapp } : {}),
        ...(sameAs.length ? { sameAs } : {}),
    };

    return (
        <Head title={seoTitle}>
            <meta name="description" content={seoDescription} />
            <link rel="canonical" href={seoUrl} />

            <meta property="og:type" content="website" />
            <meta property="og:site_name" content={siteName} />
            <meta property="og:locale" content="lo_LA" />
            <meta property="og:title" content={seoTitle} />
            <meta property="og:description" content={seoDescription} />
            <meta property="og:url" content={seoUrl} />
            <meta property="og:image" content={seoImage} />

            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" content={seoTitle} />
            <meta name="twitter:description" content={seoDescription} />
            <meta name="twitter:image" content={seoImage} />

            <script type="application/ld+json">{JSON.stringify(ldJson)}</script>
        </Head>
    );
}
