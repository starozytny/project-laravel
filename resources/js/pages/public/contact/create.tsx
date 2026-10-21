import { Form, Head } from '@inertiajs/react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';

import { privacy } from '@/routes/legal';
import { store } from '@/routes/public/contact';

export default function ContactCreate() {
    return (
        <>
            <Head title="Contact" />

            <div className="mx-auto w-full max-w-2xl px-6 py-12 sm:py-16">
                <div className="mb-10 space-y-2">
                    <h1 className="text-3xl font-semibold tracking-tight">
                        Contactez-nous
                    </h1>
                    <p className="text-muted-foreground">
                        Une question, un projet ? Remplissez le formulaire et
                        nous vous répondrons dans les meilleurs délais.
                    </p>
                </div>

                <Form
                    {...store.form()}
                    resetOnSuccess
                    options={{ preserveScroll: true }}
                    className="grid gap-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-6 sm:grid-cols-2">
                                <div className="grid gap-2">
                                    <Label htmlFor="first_name">Prénom *</Label>
                                    <Input
                                        id="first_name"
                                        name="first_name"
                                        type="text"
                                        autoComplete="given-name"
                                        required
                                        autoFocus
                                    />
                                    <InputError message={errors.first_name} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="last_name">Nom *</Label>
                                    <Input
                                        id="last_name"
                                        name="last_name"
                                        type="text"
                                        autoComplete="family-name"
                                        required
                                    />
                                    <InputError message={errors.last_name} />
                                </div>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="company">
                                    Société{' '}
                                    <span className="font-normal text-muted-foreground">
                                        (facultatif)
                                    </span>
                                </Label>
                                <Input
                                    id="company"
                                    name="company"
                                    type="text"
                                    autoComplete="organization"
                                />
                                <InputError message={errors.company} />
                            </div>

                            <div className="grid gap-6 sm:grid-cols-2">
                                <div className="grid gap-2">
                                    <Label htmlFor="phone">Téléphone *</Label>
                                    <Input
                                        id="phone"
                                        name="phone"
                                        type="tel"
                                        autoComplete="tel"
                                        required
                                    />
                                    <InputError message={errors.phone} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="email">
                                        Adresse e-mail{' '}
                                        <span className="font-normal text-muted-foreground">
                                            (facultatif)
                                        </span>
                                    </Label>
                                    <Input
                                        id="email"
                                        name="email"
                                        type="email"
                                        autoComplete="email"
                                        placeholder="email@exemple.com"
                                    />
                                    <InputError message={errors.email} />
                                </div>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="message">Message *</Label>
                                <Textarea
                                    id="message"
                                    name="message"
                                    rows={6}
                                    required
                                    minLength={10}
                                />
                                <InputError message={errors.message} />
                            </div>

                            <div className="rounded-md border bg-muted/40 p-4 text-sm text-muted-foreground">
                                <p className="mb-1 font-medium text-foreground">
                                    Protection de vos données
                                </p>
                                <p>
                                    Les informations saisies dans ce formulaire
                                    sont utilisées uniquement pour traiter votre
                                    demande et vous répondre. Elles ne sont pas
                                    transmises à des tiers et sont conservées le
                                    temps nécessaire à ce traitement. Vous
                                    disposez d&apos;un droit d&apos;accès, de
                                    rectification et de suppression de vos
                                    données. Pour en savoir plus, consultez
                                    notre{' '}
                                    <TextLink href={privacy()}>
                                        politique de confidentialité
                                    </TextLink>
                                    .
                                </p>
                            </div>

                            <div className="flex items-center justify-between gap-4">
                                <p className="text-sm text-muted-foreground">
                                    * Champs obligatoires
                                </p>
                                <Button
                                    type="submit"
                                    disabled={processing}
                                    data-test="send-contact-button"
                                >
                                    {processing && <Spinner />}
                                    Envoyer
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}
