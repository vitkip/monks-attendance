import { useMemo, useState } from 'react';
import SeoHead from '@/Components/SeoHead';

const fmt = (n) => new Intl.NumberFormat('en-US').format(Math.round(n));

function Meter({ pct }) {
    const clamped = Math.min(100, Math.max(0, pct));
    const visualPct = clamped > 0 ? Math.max(clamped, 3) : 0;

    return (
        <div className="relative h-2 rounded-full bg-black/[0.06] overflow-hidden">
            {visualPct > 0 && (
                <div
                    className="absolute inset-y-0 left-0 rounded-full transition-[width] duration-700 ease-out"
                    style={{
                        width: `${visualPct}%`,
                        backgroundImage: 'linear-gradient(to right, #8f4e00, #ff9933 55%, #ef4444)',
                        backgroundSize: `${(100 / visualPct) * 100}% 100%`,
                    }}
                />
            )}
        </div>
    );
}

function RecordCard({ monk, maxAbsence, maxFine, delay }) {
    const [loaded, setLoaded] = useState(false);
    const absencePct = (monk.absence_count / maxAbsence) * 100;
    const finePct = (monk.fine_total / maxFine) * 100;

    return (
        <div
            className="fade-up bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden p-4 flex gap-4"
            style={{ '--fade-delay': `${delay}ms` }}
        >
            <div className="relative w-16 h-16 rounded-xl bg-gray-100 overflow-hidden shrink-0 ring-1 ring-inset ring-black/5">
                {!loaded && <div className="absolute inset-0 animate-pulse bg-gray-100" aria-hidden="true"></div>}
                <img
                    src={monk.photo_url}
                    alt={monk.full_name}
                    loading="lazy"
                    decoding="async"
                    onLoad={() => setLoaded(true)}
                    className={`w-full h-full object-cover transition-opacity duration-500 ${loaded ? 'opacity-100' : 'opacity-0'}`}
                />
            </div>

            <div className="flex-1 min-w-0">
                <p className="font-semibold text-slate-800 text-sm truncate" title={monk.full_name}>
                    {monk.full_name}
                </p>
                <p className="text-[11px] text-gray-400 mb-2.5 truncate">
                    {[monk.type_label, monk.temple].filter(Boolean).join(' · ') || ' '}
                </p>

                <div className="space-y-2.5">
                    <div>
                        <div className="flex items-baseline justify-between mb-1">
                            <span className="text-[10px] font-bold uppercase tracking-wide text-gray-400">ຂາດ</span>
                            <span className="text-xs font-bold text-slate-700 tabular-nums">
                                {monk.absence_count} <span className="font-normal text-gray-400">ຄັ້ງ</span>
                            </span>
                        </div>
                        <Meter pct={absencePct} />
                    </div>

                    <div>
                        <div className="flex items-baseline justify-between mb-1">
                            <span className="text-[10px] font-bold uppercase tracking-wide text-gray-400">ຄ່າປັບ</span>
                            <span className="text-xs font-bold text-slate-700 tabular-nums">
                                {fmt(monk.fine_total)} <span className="font-normal text-gray-400">ກີບ</span>
                            </span>
                        </div>
                        <Meter pct={finePct} />
                    </div>
                </div>
            </div>
        </div>
    );
}

export default function Index({ monks, days, maxAbsence, maxFine, periodStart, periodEnd }) {
    const { totalAbsences, totalFine } = useMemo(
        () => ({
            totalAbsences: monks.reduce((sum, m) => sum + m.absence_count, 0),
            totalFine: monks.reduce((sum, m) => sum + m.fine_total, 0),
        }),
        [monks]
    );

    return (
        <div className="bg-[#faf8f2] font-sans text-slate-700 min-h-screen">
            <SeoHead
                title="ກະຕິກາ ແລະ ສະຖິຕິການປະຕິບັດທຳ"
                description="ຂໍ້ມູນສະຖິຕິການປະຕິບັດທຳ, ລະບຽບວິນັຍ, ແລະ ການຮ່ວມກິດຈະກຳຂອງພຣະສົງ, ສາມະເນນ, ຜູ້ບວດຂາວ ວັດປ່າໜອງບົວທອງໃຕ້ ປະເທດລາວ"
                keywords="ລະບຽບວັດ, ພຣະສົງ, ສາມະເນນ, ບວດຂາວ, ການປະຕິບັດທຳ, ກຳມະຖານ, ໄຫວ້ພຣະ, ວັດປ່າໜອງບົວທອງໃຕ້, ພຸດທະສາສະໜາ, ປະເທດລາວ"
            />

            <section className="relative overflow-hidden bg-brand-green-dark">
                <span
                    className="absolute -right-6 -top-14 text-[200px] leading-none text-white/[0.04] select-none pointer-events-none"
                    aria-hidden="true"
                >
                    ☸
                </span>

                <div className="relative max-w-6xl mx-auto px-5 sm:px-8 pt-10 pb-8 sm:pt-14 sm:pb-10">
                    <div className="flex items-center gap-2.5 mb-4">
                        <span className="w-8 h-8 rounded-lg bg-brand-green flex items-center justify-center shrink-0">
                            <span className="text-white text-sm select-none">☸</span>
                        </span>
                        <span className="text-xs font-bold text-white/60 uppercase tracking-widest">ວັດປ່າໜອງບົວທອງໃຕ້</span>
                    </div>

                    <h1 className="text-white text-2xl sm:text-3xl font-bold leading-tight">ສະຖິຕິການຂາດ ແລະ ຄ່າປັບ</h1>
                    <p className="text-white/60 text-sm mt-2.5 max-w-xl leading-relaxed">
                        ຂໍ້ມູນຍ້ອນຫຼັງ {days} ວັນ ({periodStart} – {periodEnd})
                    </p>

                    <div className="flex flex-wrap items-center gap-3 mt-6">
                        <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                            <span className="text-lg font-bold text-white tabular-nums leading-none">{monks.length}</span>
                            <span className="text-xs text-white/60">ອົງ · ຕິດຕາມ</span>
                        </div>
                        <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                            <span className="text-lg font-bold text-brand-bright-green tabular-nums leading-none">{totalAbsences}</span>
                            <span className="text-xs text-white/60">ຄັ້ງ · ຂາດທັງໝົດ</span>
                        </div>
                        <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                            <span className="text-lg font-bold text-brand-bright-green tabular-nums leading-none">{fmt(totalFine)}</span>
                            <span className="text-xs text-white/60">ກີບ · ຄ່າປັບທັງໝົດ</span>
                        </div>
                    </div>
                </div>
            </section>

            <div className="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">
                {monks.length === 0 ? (
                    <div className="flex flex-col items-center justify-center text-center py-24">
                        <span className="text-5xl text-brand-green/30 mb-4">☸</span>
                        <p className="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີຂໍ້ມູນ</p>
                        <p className="text-slate-400 text-sm">ບໍ່ພົບການບັນທຶກການຂາດພາຍໃນ {days} ວັນຫຼ້າສຸດ</p>
                    </div>
                ) : (
                    <>
                        <p className="text-[11px] text-gray-400 mb-5">
                            * ແຖບຄວາມຄືບໜ້າສະແດງທຽບກັບຄ່າສູງສຸດຂອງແຕ່ລະລາຍການພາຍໃນໄລຍະ {days} ວັນນີ້
                        </p>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            {monks.map((monk, index) => (
                                <RecordCard
                                    key={monk.id}
                                    monk={monk}
                                    maxAbsence={maxAbsence}
                                    maxFine={maxFine}
                                    delay={Math.min(index, 10) * 30}
                                />
                            ))}
                        </div>
                    </>
                )}
            </div>
        </div>
    );
}
