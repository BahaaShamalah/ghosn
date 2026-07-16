import { useAnimatedBars } from '../hooks';
import { useLanguage } from '../LanguageContext';
import { navLabel } from '../utils/navLabel';
import { Icon, kpiIconName } from './icons';

function KpiCard({ kpi }) {
    const { isAr } = useLanguage();

    return (
        <div className="rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 shadow-[0_6px_20px_rgba(47,67,39,0.05)]">
            <div className="mb-4 flex items-center justify-between">
                <span className="flex h-[46px] w-[46px] items-center justify-center rounded-[13px] bg-[rgba(129,149,98,0.18)] text-[#406139]">
                    <Icon name={kpiIconName(kpi.key)} className="h-[22px] w-[22px]" />
                </span>
            </div>
            <div className="text-[28px] font-bold leading-none text-[#2f4327]">{kpi.value}</div>
            <div className="mt-[5px] text-[13px] text-[#8a9280]">{isAr ? kpi.label_ar : kpi.label_en}</div>
        </div>
    );
}

function DonationChart({ trend }) {
    const { t, isAr } = useLanguage();
    const chartRef = useAnimatedBars('[data-chartbar]', trend);

    return (
        <div className="rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 shadow-[0_6px_20px_rgba(47,67,39,0.05)]">
            <div className="mb-2 flex items-center justify-between">
                <h3 className="m-0 text-base font-bold text-[#2f4327]">{t.chart.title}</h3>
                <span className="text-[12.5px] text-[#8a9280]">{t.chart.sub}</span>
            </div>
            <div ref={chartRef} className="flex h-[180px] items-end gap-2.5 pt-5">
                {trend.map((month) => (
                    <div key={month.label_en} className="flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <div className="flex h-full w-full items-end">
                            <div
                                data-chartbar
                                data-height={month.heightPct}
                                title={`$${month.amount.toLocaleString()}`}
                                className="gh-admin-chart-bar w-full rounded-t-[7px] bg-gradient-to-t from-[#406139] to-[#819562]"
                                style={{ height: 0 }}
                            />
                        </div>
                        <span className="text-[11px] font-semibold text-[#8a9280]">
                            {isAr ? month.label_ar : month.label_en}
                        </span>
                    </div>
                ))}
            </div>
        </div>
    );
}

function CampaignProgress({ campaigns }) {
    const { isAr, t } = useLanguage();
    const progressRef = useAnimatedBars('[data-progressbar]', campaigns);

    return (
        <div className="rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 shadow-[0_6px_20px_rgba(47,67,39,0.05)]">
            <h3 className="mb-[18px] m-0 text-base font-bold text-[#2f4327]">{t.progress.title}</h3>
            {campaigns.length === 0 ? (
                <p className="text-sm text-[#8a9280]">{t.emptyCampaigns}</p>
            ) : (
                <div ref={progressRef} className="flex flex-col gap-[18px]">
                    {campaigns.map((campaign) => (
                        <div key={campaign.url}>
                            <div className="mb-[7px] flex items-baseline justify-between">
                                <a href={campaign.url} className="text-[13.5px] font-semibold text-[#3a4234] no-underline hover:text-[#406139]">
                                    {isAr ? campaign.title_ar : campaign.title_en}
                                </a>
                                <span className="text-[12.5px] font-bold text-[#406139]">{campaign.pct}%</span>
                            </div>
                            <div className="h-2 overflow-hidden rounded-full bg-[rgba(64,97,57,0.12)]">
                                <div
                                    data-progressbar
                                    data-pct={campaign.pct}
                                    className="gh-admin-progress-bar h-full rounded-full bg-gradient-to-r from-[#819562] to-[#406139]"
                                    style={{ width: 0 }}
                                />
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

function RecentDonations({ donations }) {
    const { isAr, t, routes } = useLanguage();

    return (
        <div className="rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 shadow-[0_6px_20px_rgba(47,67,39,0.05)]">
            <div className="mb-4 flex items-center justify-between">
                <h3 className="m-0 text-base font-bold text-[#2f4327]">{t.recentDonations}</h3>
                <a href={routes.donations} className="border-none bg-transparent text-[13px] font-semibold text-[#406139] no-underline">
                    {t.viewAll}
                </a>
            </div>
            {donations.length === 0 ? (
                <p className="text-sm text-[#8a9280]">{t.emptyDonations}</p>
            ) : (
                <div className="flex flex-col gap-3">
                    {donations.map((donation, index) => (
                        <div key={`${donation.name}-${index}`} className="flex items-center gap-3">
                            <span
                                className="flex h-[38px] w-[38px] shrink-0 items-center justify-center rounded-full text-sm font-bold text-[#F7F6F0]"
                                style={{ background: donation.color }}
                            >
                                {donation.initial}
                            </span>
                            <div className="min-w-0 flex-1">
                                <div className="text-[13.5px] font-semibold text-[#3a4234]">{donation.name}</div>
                                <div className="text-xs text-[#8a9280]">
                                    {isAr ? donation.campaign_ar : donation.campaign_en}
                                </div>
                            </div>
                            <span className="text-[14.5px] font-bold text-[#406139]">{donation.amount}</span>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

function RecentVolunteers({ volunteers }) {
    const { isAr, t, routes } = useLanguage();
    const statusClass = {
        pending: 'bg-[rgba(191,160,74,0.16)] text-[#8a6d1f]',
        approved: 'bg-[rgba(64,97,57,0.14)] text-[#33502e]',
        rejected: 'bg-[rgba(162,74,55,0.13)] text-[#8a3d2d]',
    };

    return (
        <div className="rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 shadow-[0_6px_20px_rgba(47,67,39,0.05)]">
            <div className="mb-4 flex items-center justify-between">
                <h3 className="m-0 text-base font-bold text-[#2f4327]">{t.recentVolunteers}</h3>
                <a href={routes.volunteers} className="border-none bg-transparent text-[13px] font-semibold text-[#406139] no-underline">
                    {t.viewAll}
                </a>
            </div>
            {volunteers.length === 0 ? (
                <p className="text-sm text-[#8a9280]">{t.emptyVolunteers}</p>
            ) : (
                <div className="flex flex-col gap-3">
                    {volunteers.map((volunteer, index) => (
                        <div key={`${volunteer.name}-${index}`} className="flex items-center gap-3">
                            <span className="flex h-[38px] w-[38px] shrink-0 items-center justify-center rounded-full bg-[rgba(129,149,98,0.18)] text-sm font-bold text-[#406139]">
                                {volunteer.initial}
                            </span>
                            <div className="min-w-0 flex-1">
                                <div className="text-[13.5px] font-semibold text-[#3a4234]">{volunteer.name}</div>
                                <div className="text-xs text-[#8a9280]">{isAr ? volunteer.area_ar : volunteer.area_en}</div>
                            </div>
                            <span className={`rounded-full px-2.5 py-1 text-[11px] font-bold ${statusClass[volunteer.status] ?? statusClass.pending}`}>
                                {t.status[volunteer.status] ?? volunteer.status}
                            </span>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

function QuickLinks() {
    const { t, routes, isAr, nav } = useLanguage();

    const links = [
        { href: routes.settings, label: navLabel(nav, 'settings', isAr) },
        { href: routes.pages, label: navLabel(nav, 'pages', isAr) },
        ...(routes.homepageBuilder ? [{ href: routes.homepageBuilder, label: t.homepageBuilder }] : []),
    ].filter((link) => link.label);

    return (
        <div className="rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-gradient-to-br from-[#406139] to-[#33502e] p-6 text-[#F2F1EA] shadow-[0_6px_20px_rgba(47,67,39,0.12)]">
            <p className="text-xs font-semibold uppercase tracking-[0.18em] text-[#cdd8bf]">{t.quickLinks}</p>
            <div className="mt-4 flex flex-wrap gap-2">
                {links.map((link) => (
                    <a
                        key={link.href}
                        href={link.href}
                        className="rounded-full bg-[rgba(242,241,234,0.15)] px-4 py-2 text-sm font-medium text-[#F2F1EA] no-underline transition hover:bg-[rgba(242,241,234,0.25)]"
                    >
                        {link.label}
                    </a>
                ))}
            </div>
        </div>
    );
}

export function Dashboard() {
    const { dashboard, t } = useLanguage();
    const { kpis = [], trend = [], campaignProgress = [], recentDonations = [], recentVolunteers = [] } = dashboard;

    return (
        <div className="gh-admin-fade px-5 py-7 md:px-[30px] md:pb-10">
            <div className="mb-[26px] grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                {kpis.map((kpi) => (
                    <KpiCard key={kpi.key} kpi={kpi} />
                ))}
            </div>

            <div className="mb-5 grid grid-cols-1 gap-5 xl:grid-cols-[1.6fr_1fr]">
                <DonationChart trend={trend} />
                <CampaignProgress campaigns={campaignProgress} />
            </div>

            <div className="mb-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
                <RecentDonations donations={recentDonations} />
                <RecentVolunteers volunteers={recentVolunteers} />
            </div>

            <div className="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <QuickLinks />
                <div className="rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[rgba(237,238,228,0.5)] p-6">
                    <h2 className="text-lg font-bold text-[#2f4327]">{t.nextSteps}</h2>
                    <p className="mt-2 max-w-2xl text-sm leading-relaxed text-[#5f6857]">{t.nextStepsBody}</p>
                </div>
            </div>
        </div>
    );
}
