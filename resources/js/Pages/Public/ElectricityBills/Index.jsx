import { Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import SeoHead from '@/Components/SeoHead';
import CopyAccountButton from '@/Components/CopyAccountButton';

const LAO_MONTHS = {
    1: 'ມັງກອນ', 2: 'ກຸມພາ', 3: 'ມີນາ', 4: 'ເມສາ',
    5: 'ພຶດສະພາ', 6: 'ມິຖຸນາ', 7: 'ກໍລະກົດ', 8: 'ສິງຫາ',
    9: 'ກັນຍາ', 10: 'ຕຸລາ', 11: 'ພະຈິກ', 12: 'ທັນວາ',
};

const fmt = (n) => new Intl.NumberFormat('en-US').format(n);

export default function Index({ billsByMonth, availableYears, year, monthly, totalYear, countYear, totalAllTime }) {
    const maxMonthly = monthly.length ? Math.max(...monthly) : 0;
    const hasMonthlyData = maxMonthly > 0;

    return (
        <PublicLayout>
            <SeoHead title="ລາຍຈ່າຍຄ່າໄຟຟ້າ" description="ລາຍງານຄ່າໄຟຟ້າຂອງວັດປ່າໜອງບົວທອງໃຕ້ ແບບໂປ່ງໃສ" />

            {/* Hero */}
            <section className="relative overflow-hidden bg-brand-green-dark">
                <span
                    className="absolute -right-6 -top-14 text-[240px] leading-none text-white/[0.04] select-none pointer-events-none"
                    aria-hidden="true"
                >
                    ⚡
                </span>

                <div className="relative max-w-6xl mx-auto px-5 sm:px-8 pt-16 pb-10 sm:pt-20 sm:pb-12">
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/10 text-brand-bright-green mb-4">
                        <span aria-hidden="true">⚡</span> ຄວາມໂປ່ງໃສທາງການເງິນ
                    </span>

                    <h1 className="text-white text-3xl sm:text-4xl font-bold leading-tight">
                        ເຊີນຮ່ວມບໍລິຈາກຄ່າໄຟຟ້າ ຕາມເລກລະຫັດບິນ
                    </h1>
                    <p className="text-white/60 text-sm sm:text-base mt-3 max-w-xl leading-relaxed">
                        ບັນທຶກໃບບິນຄ່າໄຟຟ້າຂອງວັດໃນແຕ່ລະເດືອນ ເປີດເຜີຍໃຫ້ຍາດໂຍມ ແລະ ຜູ້ມີສັດທາຮ່ວມສົມທົບໄດ້
                    </p>

                    <div className="flex flex-wrap items-center gap-3 mt-7">
                        <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                            <span className="text-lg font-bold text-white tabular-nums leading-none">{fmt(totalYear)}</span>
                            <span className="text-xs text-white/60">ກີບ · ຍອດລວມປີ {year}</span>
                        </div>
                        <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                            <span className="text-lg font-bold text-brand-bright-green tabular-nums leading-none">{countYear}</span>
                            <span className="text-xs text-white/60">ໃບ · ຈຳນວນບິນ</span>
                        </div>
                    </div>
                    <p className="text-white/30 text-xs mt-4">ຍອດລວມທັງໝົດທຸກປີ: {fmt(totalAllTime)} ກີບ</p>

                    {/* Monthly summary bars — the year's spending shape at a glance */}
                    {hasMonthlyData && (
                        <div className="mt-9 bg-white/[0.04] rounded-2xl px-5 pt-8 pb-3">
                            <div className="flex items-end gap-1.5 sm:gap-2.5 h-24">
                                {monthly.map((value, index) => {
                                    const monthNum = index + 1;
                                    const isPeak = value > 0 && value === maxMonthly;
                                    const heightPct = maxMonthly > 0 ? Math.max((value / maxMonthly) * 100, value > 0 ? 6 : 2) : 2;

                                    return (
                                        <div key={index} className="relative flex-1 h-full flex flex-col items-center justify-end group">
                                            {isPeak && (
                                                <span className="absolute -top-5 text-[10px] font-bold text-brand-bright-green whitespace-nowrap">
                                                    {fmt(value)}
                                                </span>
                                            )}
                                            <div
                                                title={`${String(monthNum).padStart(2, '0')}/${year} — ${fmt(value)} ກີບ`}
                                                className={`w-full rounded-t-[3px] transition-all duration-300 ${
                                                    isPeak ? 'bg-brand-bright-green' : 'bg-white/15 group-hover:bg-white/25'
                                                }`}
                                                style={{ height: `${heightPct}%` }}
                                            ></div>
                                        </div>
                                    );
                                })}
                            </div>
                            <div className="flex items-center gap-1.5 sm:gap-2.5 mt-2">
                                {monthly.map((_, index) => (
                                    <span key={index} className="flex-1 text-center text-[9px] text-white/35 tabular-nums">
                                        {String(index + 1).padStart(2, '0')}
                                    </span>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </section>

            <div className="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">
                {/* Year filter */}
                {availableYears.length > 1 && (
                    <nav
                        aria-label="ເລືອກປີ"
                        className="sticky top-16 z-10 -mx-5 px-5 sm:mx-0 sm:px-0 py-3 mb-10 bg-[#faf8f2]/90 backdrop-blur-sm border-b border-black/5"
                    >
                        <div className="flex items-center gap-2 overflow-x-auto no-scrollbar sm:flex-wrap">
                            {availableYears.map((y) => (
                                <Link
                                    key={y}
                                    href={route('electricity-bills.public.index', { year: y })}
                                    preserveScroll
                                    className={`shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green ${
                                        Number(year) === Number(y)
                                            ? 'bg-brand-green text-white'
                                            : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50'
                                    }`}
                                >
                                    ປີ {y}
                                </Link>
                            ))}
                        </div>
                    </nav>
                )}

                {billsByMonth.length === 0 ? (
                    /* Empty state */
                    <div className="flex flex-col items-center justify-center text-center py-24">
                        <span className="text-5xl text-brand-green/30 mb-4">⚡</span>
                        <p className="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີຂໍ້ມູນໃນປີ {year}</p>
                        <p className="text-slate-400 text-sm">ກະລຸນາເລືອກປີອື່ນ ຫຼື ກັບມາເບິ່ງໃໝ່ພາຍຫຼັງ</p>
                    </div>
                ) : (
                    <>
                        {/* Ledger, grouped by month */}
                        <div className="space-y-8">
                            {billsByMonth.map((group) => (
                                <div key={group.month}>
                                    {/* Month header */}
                                    <div className="flex items-center gap-3 mb-3">
                                        <div className="flex items-center gap-2.5 shrink-0">
                                            <div className="w-8 h-8 rounded-lg bg-brand-light-green flex items-center justify-center">
                                                <span className="text-brand-green text-sm">⚡</span>
                                            </div>
                                            <div>
                                                <p className="text-sm font-bold text-slate-800 leading-none">
                                                    ເດືອນ{LAO_MONTHS[group.month_num]} {year}
                                                </p>
                                                <p className="text-[10px] text-gray-400 mt-0.5 tabular-nums">
                                                    {String(group.month_num).padStart(2, '0')}/{year} · {group.count} ໃບ
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex-1 h-px bg-gray-200"></div>
                                        <span className="text-xs font-bold text-brand-green tabular-nums px-2.5 py-1 bg-brand-light-green rounded-full whitespace-nowrap">
                                            {fmt(group.total)} ກີບ
                                        </span>
                                    </div>

                                    <div className="bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden">
                                        {group.bills.map((bill, index) => (
                                            <div
                                                key={bill.id}
                                                className={`flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-4 ${
                                                    index !== group.bills.length - 1 ? 'border-b border-gray-100' : ''
                                                }`}
                                            >
                                                <span className="hidden sm:flex w-7 h-7 shrink-0 rounded-full bg-gray-50 text-gray-400 text-[11px] font-bold items-center justify-center tabular-nums">
                                                    {String(index + 1).padStart(2, '0')}
                                                </span>

                                                <a
                                                    href={bill.image_url}
                                                    target="_blank"
                                                    rel="noopener"
                                                    className="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 shrink-0 border border-gray-200"
                                                >
                                                    <img
                                                        src={bill.image_url}
                                                        alt={`ໃບບິນ ${bill.customer_name} ເດືອນ ${bill.bill_month_label}`}
                                                        className="w-full h-full object-cover"
                                                    />
                                                </a>

                                                <div className="flex-1 min-w-0">
                                                    <p className="font-bold text-slate-800 text-sm truncate">{bill.customer_name}</p>
                                                    <p className="text-gray-400 text-xs mt-0.5">
                                                        {bill.province} &nbsp;·&nbsp; ບັນຊີຜູ້ໃຊ້ໄຟ{' '}
                                                        <CopyAccountButton value={bill.account_number} />
                                                    </p>
                                                </div>

                                                <div className="text-right shrink-0">
                                                    <p className="font-bold text-slate-800 text-sm tabular-nums">{fmt(bill.amount)} ກີບ</p>
                                                    <p className="text-gray-400 text-xs mt-0.5 tabular-nums">{bill.bill_month_label}</p>
                                                </div>

                                                <a
                                                    href={bill.image_url}
                                                    target="_blank"
                                                    rel="noopener"
                                                    className="hidden md:inline-flex shrink-0 items-center gap-1 text-xs font-semibold text-brand-green hover:text-brand-bright-green transition-colors ml-1"
                                                    aria-label={`ເບິ່ງໃບບິນ ${bill.customer_name}`}
                                                >
                                                    ເບິ່ງໃບບິນ
                                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M8 4l6 6-6 6" />
                                                    </svg>
                                                </a>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            ))}
                        </div>

                        {/* Year total — the ledger's bottom line */}
                        <div className="flex items-center justify-between gap-4 mt-8 px-5 py-4 bg-brand-green-dark rounded-2xl">
                            <span className="text-sm font-bold text-white/80">ລວມຄ່າໄຟຟ້າທັງໝົດປີ {year}</span>
                            <span className="text-base font-bold text-brand-bright-green tabular-nums">{fmt(totalYear)} ກີບ</span>
                        </div>
                    </>
                )}
            </div>
        </PublicLayout>
    );
}
