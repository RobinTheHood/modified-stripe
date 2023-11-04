# Inhalt
📦 Installation<br>
🔧 Konfiguration des Stripe-Zahlungsmoduls<br>
🔄 Änderungen und neue Dateien<br>
🌟 Update<br>
❌ Deinstallation

# 📦 Installation
Für die Installation benötigen Sie ca. 15 Minuten.

## Hinweis
Stellen Sie sicher, dass Sie vor der Installation dieses Moduls ein vollständiges Backup Ihrer modified Shop Datenbank und Dateien erstellen.

## Das Modul im Adminbereich installieren
1. Gehen Sie in den MMLC und Installieren Sie das Modul.
2. Melden Sie sich im Adminbereich an.
3. Gehen Sie im Menü zu **Module > Zahlungsoptionen**.
4. Wählen Sie dort das Modul **Stripe Zahlungsmodul** aus.
5. Klicken Sie rechts auf den Button Installieren.
6. Lesen Sie die Installationsanleitungen der abhängigen Module unter Details im MMLC (falls vorhanden).

## 🔧 Konfiguration des Stripe-Zahlungsmoduls

1. Melden Sie sich im Adminbereich an.
2. Gehen Sie im Menü zu **Module > Zahlungsoptionen**.
3. Wählen Sie dort das Modul **Stripe Zahlungsmodul** aus.
4. Klicken Sie rechts auf den Button Bearbeiten.
5. Geben Sie Ihre Stripe-API-Schlüssel und andere erforderliche Informationen ein.
6. Stellen Sie sicher, dass Sie den Test-Modus verwenden, wenn Sie das Modul zunächst testen möchten.
7. Speichern Sie die Einstellungen.

### Plugin Konfiguration
Im Stripe Zahlungsmodul stehen Ihnen wichtige Konfigurationsmöglichkeiten zur Verfügung, um Ihre Zahlungsabwicklung optimal anzupassen. Hier finden Sie eine Übersicht der verfügbaren Felder und deren Verwendung. Stellen Sie sicher, dass Sie die richtigen Schlüssel im entsprechenden Modus verwenden, um reibungslose und sichere Zahlungen zu gewährleisten.

#### Allgemeine Einstellungen

- **Livemode aktiviert:** Mit diesem Feld können Sie zwischen dem Live-Modus und dem Test-Modus umschalten. Wenn Sie Ihren Shop im Live-Modus betreiben, setzen Sie diesen Wert auf "ja". Im Test-Modus können Sie Ihre Zahlungsabwicklung testen, indem Sie "nein" auswählen.

#### Test-Modus (Standbox-Modus)
Standardmäßig befindet Sie Ihr Stripe Account im Test-Modus (Sandbox-Modus), nachdem Sie sich einen Stripe Account erstellt haben. In diesem Modus haben Sie die Möglichkeit Zahlungen zu simulieren und zu testen, ohne echtes Geld oder echte Transaktionen zu verwenden. Wenn Sie Ihr Zahlungsmodul oder Ihren Shop entwickeln und testen möchten, verwenden Sie den Test-Modus. Stripe stellt [Testkarten und Testkonten](https://stripe.com/docs/testing?locale=de-DE) zur Verfügung, damit Sie Ihre Zahlungsabwicklung validieren können. Alle Zahlungen, die im Test-Modus getätigt werden, sind nicht echt und haben keine finanziellen Auswirkungen.

- **Veröffentlichbarer Schlüssel im Test-Modus:** Dieser Schlüssel dient zu Testzwecken im clientseitigen Code Ihres Shops. Verwenden Sie diesen Schlüssel, wenn Sie Ihre Zahlungsabwicklung im Test-Modus überprüfen möchten. Sie finden diesen Schlüssel im Stripe-Dashboard unter "Entwickler" > "API-Schlüssel" im Feld "Veröffentlichbarer Schlüssel".

- **Geheimschlüssel im Test-Modus:** Dieser Schlüssel authentifiziert Anfragen auf Ihrem Server im Test-Modus. Standardmäßig ermöglicht er Ihnen, API-Anfragen ohne Einschränkungen durchzuführen. Sie finden diesen Schlüssel ebenfalls im Stripe-Dashboard unter "Entwickler" > "API-Schlüssel" im Feld "Geheimschlüssel".

#### Live Modus
Um in den Live Modus wechseln zu könnne, müssen Sie in Ihrem Stripe Account den Live Modus aktivieren. Der Live-Modus ist die Produktionsumgebung, in der Sie echte Zahlungen von echten Kunden verarbeiten. Wenn Sie bereit sind Ihren modified Shop oder das Stripe Zahlungsmodul für den öffentlichen Gebrauch freizugeben, wechseln Sie in den Live-Modus, indem Sie Ihre echten Stripe-API-Schlüssel verwenden. In dieser Umgebung werden tatsächliche Transaktionen durchgeführt und echtes Geld wird zwischen Kunden und Händlern bewegt. Sie müssen den Live-Modus im Stripe Zahlungsmodul und in Ihrem Stripe Account aktivieren.

- **Veröffentlichbarer Schlüssel im Live-Modus:** Wenn Sie bereit sind, Ihren Shop oder Ihr Stripe Zahlungsmodul im Live-Modus zu betreiben, verwenden Sie diesen Schlüssel im clientseitigen Code. Sie finden ihn im Stripe-Dashboard unter "Entwickler" > "API-Schlüssel" im Feld "Veröffentlichbarer Schlüssel".

- **Geheimschlüssel für den Live-Modus:** Dieser Schlüssel authentifiziert Anfragen auf Ihrem Server im Live-Modus. Ähnlich wie im Test-Modus können Sie mit diesem Schlüssel API-Anfragen ohne Einschränkungen durchführen. Sie finden ihn im Stripe-Dashboard unter "Entwickler" > "API-Schlüssel" im Feld "Geheimschlüssel", wenn Sie Stripe im Live-Modus betreiben.

#### Stripe Webhooks Konfigurieren
Einige Zahlungen, wie beispielsweise SEPA-Lastschrift oder SOFORT Überweisung, erfordern einige Tage, um den endgültigen Zahlungsstatus zu bestätigen. Stripe kann Ihrem Shop nicht unmittelbar mitteilen, ob die Zahlung erfolgreich war. Der Zahlungsstatus bleibt daher offen, bis das Geld auf Ihrem Konto bzw. bei Stripe eingegangen ist. Um sicherzustellen, dass Stripe den Zahlungsstatus automatisch in Ihrem modified Shop aktualisiert, bietet das Stripe Zahlungsmodul die Möglichkeit der Stripe Webhooks an.

Webhooks sind Benachrichtigungen, die von Stripe an Ihren modified Shop gesendet werden, um bestimmte Ereignisse wie erfolgreiche Zahlungen oder Rückerstattungen zu melden. Um Webhooks zu verwenden, müssen Sie einen Endpunkt auf Stripe einrichten. Beachten Sie, dass die genauen Schritte im Stripe-Dashboard leicht variieren können, aber in der Regel folgen sie einem ähnlichen Prozess wie folgt:

1. Gehen Sie zu Ihrem Stripe-Dashboard und klicken Sie auf "Entwickler" > "Webhooks".
2. Klicken Sie auf die Schaltfläche "Endpoint hinzufügen".
3. Geben Sie die URL für den Webhook-Endpunkt ein: `www.meinshop.de/rth_stripe.php?action=receiveHook`. Ersetzen Sie `www.meinshop.de` mit Ihrer Shop-Url.
4. Wählen Sie die Ereignisse aus, für die Sie Benachrichtigungen erhalten möchten. In den meisten Fällen möchten Sie zumindest `charge.succeeded` auswählen, um erfolgreiche Zahlungen zu verfolgen.
5. Speichern Sie den Webhook-Endpunkt und aktivieren Sie ihn.
6. Gehen Sie in Ihrem Shop in die Einstellungen zum **Stripe Zahlungsmodul**.
7. Hinterlegen Sie dort den **Geheimer Webhook Schlüssel**, den Sie auf der Stripe Webseite bei Ihrem neuen Webhook finden. Dieser Schlüssel wird benötigt, damit Ihr modified Shop verifizieren kann, dass die Hook-Anfragen tatsächlich von Stripe kommen.

## 🔄 Änderungen und neue Dateien
Folgende Änderungen und Dateien wurden an Ihrem Shop bei der Installation verändert.

### Datenbank
Bei der Installation werden folgende Tabellen und Spalten hinzugefügt.
- `rth_stripe_payment`
- `rth_stripe_php_session`

# 🌟 Update
1. Gehen Sie in den MMLC und aktualisieren Sie das Modul.
2. Melden Sie sich im Adminbereich an.
3. Gehen Sie im Menü zu **Module > Zahlungsoptionen**.
4. Wählen Sie dort das Modul **Stripe Zahlungsmodul** aus.
5. Klicken rechts auf den Button Update (falls vorhanden).

# ❌ Deinstallation
1. Melden Sie sich im Adminbereich an.
2. Gehen Sie im Menü zu **Module > Zahlungsoptionen**.
3. Wählen Sie dort das Modul **Stripe Zahlungsmodul** aus.
4. Klicken Sie rechts auf den Button Deinstallieren.
5. Gehen Sie in den MMLC und deinstallieren Sie das Modul.

Bei der Deinstallation werden die neu angelegten Tabellen und Spalten in der Datenbank entfernt.