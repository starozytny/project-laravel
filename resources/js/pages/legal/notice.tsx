import { LegalPage, LegalSection } from '@/components/public/legal-page';

export default function Notice() {
    return (
        <LegalPage title="Mentions légales">
            <LegalSection title="Éditeur du site">
                <p>
                    [Raison sociale ou nom], [forme juridique] au capital de
                    [montant] €, immatriculée sous le numéro [SIREN / RCS].
                </p>
                <p>Siège social : [adresse complète].</p>
                <p>Contact : [adresse e-mail] — [numéro de téléphone].</p>
            </LegalSection>

            <LegalSection title="Directeur de la publication">
                <p>[Nom et prénom du directeur de la publication].</p>
            </LegalSection>

            <LegalSection title="Hébergeur">
                <p>
                    [Nom de l'hébergeur], [adresse complète], [numéro de
                    téléphone ou site web].
                </p>
            </LegalSection>

            <LegalSection title="Propriété intellectuelle">
                <p>
                    L'ensemble des contenus de ce site (textes, images, logos,
                    éléments graphiques) est protégé par le droit de la
                    propriété intellectuelle. Toute reproduction, même
                    partielle, sans autorisation préalable est interdite.
                </p>
            </LegalSection>

            <LegalSection title="Responsabilité">
                <p>
                    L'éditeur s'efforce de fournir des informations exactes et à
                    jour, mais ne peut garantir l'absence d'erreur ou
                    d'omission. L'utilisation du site se fait sous la seule
                    responsabilité de l'utilisateur.
                </p>
            </LegalSection>
        </LegalPage>
    );
}
