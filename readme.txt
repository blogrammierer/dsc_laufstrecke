###
# dsc_laufstrecken Wordpress Plugin
##

# Voraussetzungen
Für die Installation des Plugins ist das Plugin "Advanced Custom Fields" (https://wordpress.org/plugins/advanced-custom-fields/) notwendig.
Das Plugin muss installiert sein und die Felder aus der Datei "advanced-custom-field-export.xml" importieren. Die XML-Datei liegt im Plugin-Ordner.

# Verwendung
Das Plugin sorgt dafür, dass neue Shortcodes zur Verfügung gestellt werden. 
Neben den Shortcodes wird ebenfalls ein neuer CPT "Laufstrecke" erstellt.

In der folgenden Reihenfolge sollten diese Shortcodes auf der gewünschten Seite eingebettet werden:

[dsc_map_init]
[dsc_map_form]
[dsc_cta_button]
[dsc_feed]

Der [dsc_cta_widget] ist für die Verwendung in der rechten Spalte angedacht.

# Shortcode Beschreibung
[dsc_map_init]
    Initialisiert die Google Maps Karte, sowie Filter und Suche.

[dsc_map_form]
    Fügt das Formular ein, welches die Möglichkeit anbietet neue Laufstrecken einzutragen.
    
[dsc_cta_button]
    Der "Jetzt mitmachen" Button wird durch diesen Shortcode integriert.
    
[dsc_feed]
    Der Feed listet alle bestehenden Laufstrecken auf und führt bei Click zum Eintrag auf die Karte.
    
[dsc_cta_widget] 
    Grünes "Widget"-ähnliche Teasergrafik, die als CTA für das Formular zum Eintragen dient
    
    

# Rückfragen
Bei Rückfragen können Sie sich gerne an me@dennis-schlobohm.de wenden.