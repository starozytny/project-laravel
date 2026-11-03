const DISPLAY_TIME_ZONE = 'Europe/Paris';

const dateTimeFormat = new Intl.DateTimeFormat('fr-FR', {
    dateStyle: 'medium',
    timeStyle: 'short',
    timeZone: DISPLAY_TIME_ZONE,
});

export function formatDateTime(isoDate: string): string {
    return dateTimeFormat.format(new Date(isoDate));
}
