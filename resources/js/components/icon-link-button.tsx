import { Link } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import type { ComponentProps } from 'react';

import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

type Props = {
    href: ComponentProps<typeof Link>['href'];
    icon: LucideIcon;
    label: string;
    ariaLabel?: string;
};

export default function IconLinkButton({
    href,
    icon: Icon,
    label,
    ariaLabel = label,
}: Props) {
    return (
        <Tooltip delayDuration={300}>
            <TooltipTrigger asChild>
                <Button variant="ghost" size="icon" asChild>
                    <Link href={href} aria-label={ariaLabel}>
                        <Icon />
                    </Link>
                </Button>
            </TooltipTrigger>
            <TooltipContent>{label}</TooltipContent>
        </Tooltip>
    );
}
