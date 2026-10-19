import type { LucideIcon } from 'lucide-react';
import type { ComponentProps } from 'react';

import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

type Props = Omit<ComponentProps<typeof Button>, 'children'> & {
    icon: LucideIcon;
    label: string;
    ariaLabel?: string;
};

export default function IconButton({
    icon: Icon,
    label,
    ariaLabel = label,
    ...props
}: Props) {
    return (
        <Tooltip delayDuration={300}>
            <TooltipTrigger asChild>
                <Button
                    variant="ghost"
                    size="icon"
                    aria-label={ariaLabel}
                    {...props}
                >
                    <Icon />
                </Button>
            </TooltipTrigger>
            <TooltipContent>{label}</TooltipContent>
        </Tooltip>
    );
}
