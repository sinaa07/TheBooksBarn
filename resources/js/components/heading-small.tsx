export default function HeadingSmall({ title, description }: { title: string; description?: string }) {
    return (
        <header>
            <h3 className="mb-0.5 text-base font-medium text-amber-900">{title}</h3>
            {description && <p className="text-muted-foreground text-sm text-amber-900">{description}</p>}
        </header>
    );
}
