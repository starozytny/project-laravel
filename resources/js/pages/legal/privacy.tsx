import { LegalPage, LegalSection } from '@/components/public/legal-page';

export default function Privacy() {
    return (
        <LegalPage title="Politique de confidentialité">
            <LegalSection title="Responsable du traitement">
                <p>
                    Les données personnelles collectées sur ce site sont
                    traitées par [raison sociale ou nom], [adresse complète].
                </p>
                <p>Contact : [adresse e-mail du responsable ou du DPO].</p>
            </LegalSection>

            <LegalSection title="Données collectées">
                <p>
                    Nous collectons uniquement les données nécessaires au
                    fonctionnement du service : [nom, adresse e-mail, mot de
                    passe chiffré, données de connexion, ...].
                </p>
            </LegalSection>

            <LegalSection title="Finalités et bases légales">
                <p>
                    Ces données sont utilisées pour [gérer les comptes
                    utilisateurs, répondre aux demandes, assurer la sécurité du
                    service, ...], sur la base de [l'exécution du contrat, de
                    votre consentement, de notre intérêt légitime].
                </p>
            </LegalSection>

            <LegalSection title="Durée de conservation">
                <p>
                    Les données sont conservées pendant [durée] à compter de [la
                    fin de la relation, du dernier contact, ...], puis
                    supprimées ou anonymisées.
                </p>
            </LegalSection>

            <LegalSection title="Destinataires">
                <p>
                    Vos données ne sont accessibles qu'aux personnes habilitées
                    et à nos sous-traitants ([hébergeur, service d'envoi
                    d'e-mails, ...]), dans le respect de la réglementation.
                </p>
            </LegalSection>

            <LegalSection title="Vos droits">
                <p>
                    Vous disposez d'un droit d'accès, de rectification,
                    d'effacement, de limitation, d'opposition et de portabilité
                    de vos données. Pour les exercer, écrivez-nous à [adresse
                    e-mail]. Vous pouvez aussi introduire une réclamation auprès
                    de la CNIL (cnil.fr).
                </p>
            </LegalSection>

            <LegalSection title="Cookies">
                <p>
                    Ce site utilise [uniquement des cookies strictement
                    nécessaires à son fonctionnement (session, préférences
                    d'affichage)]. Aucun cookie publicitaire n'est déposé.
                </p>
            </LegalSection>
        </LegalPage>
    );
}
