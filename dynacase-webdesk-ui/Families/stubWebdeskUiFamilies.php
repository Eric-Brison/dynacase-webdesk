<?php
namespace Dcp\Family {
	/** Service Portail  */
	class Portal_service extends \Dcp\WebdeskUi\Portal_Service { const familyName="PORTAL_SERVICE";}
	/** Portail utilisateur  */
	class User_portal extends \Dcp\WebdeskUi\User_Portal { const familyName="USER_PORTAL";}
}
namespace Dcp\AttributeIdentifiers {
	/** Service Portail  */
	class Portal_service {
		/** [frame] Service */
		const psvc_spec='psvc_spec';
		/** [text] Titre */
		const psvc_title='psvc_title';
		/** [longtext] Description */
		const psvc_descr='psvc_descr';
		/** [text] Consultation */
		const psvc_vurl='psvc_vurl';
		/** [text] Édition */
		const psvc_eurl='psvc_eurl';
		/** [text] Javascript */
		const psvc_jsfile='psvc_jsfile';
		/** [text] Css */
		const psvc_cssfile='psvc_cssfile';
		/** [file] Icone */
		const psvc_icon='psvc_icon';
		/** [enum] Catégorie */
		const psvc_categorie='psvc_categorie';
		/** [enum] Délai de rafraîchissement */
		const psvc_refreshd='psvc_refreshd';
		/** [frame] Paramètres */
		const psvc_params='psvc_params';
		/** [enum] Disponible */
		const psvc_available='psvc_available';
		/** [enum] Modifiable */
		const psvc_umode='psvc_umode';
		/** [enum] Obligatoire */
		const psvc_mandatory='psvc_mandatory';
		/** [enum] Interactif */
		const psvc_interactif='psvc_interactif';
		/** [text] Application attendue */
		const psvc_appneeded='psvc_appneeded';
	}
	/** Portail utilisateur  */
	class User_portal {
		/** [frame] Portail */
		const uport_descr='uport_descr';
		/** [text] Titre */
		const uport_title='uport_title';
		/** [docid] Propriétaire Id */
		const uport_ownerid='uport_ownerid';
		/** [text] Propriétaire */
		const uport_owner='uport_owner';
		/** [array] Services */
		const uport_tservices='uport_tservices';
		/** [int] Service Number */
		const uport_svcnum='uport_svcnum';
		/** [docid] Id Service */
		const uport_idsvc='uport_idsvc';
		/** [text] Service */
		const uport_svc='uport_svc';
		/** [text] Paramêtres */
		const uport_param='uport_param';
		/** [int] Colonne */
		const uport_column='uport_column';
		/** [int] Ligne */
		const uport_line='uport_line';
		/** [enum] Ouvert */
		const uport_open='uport_open';
		/** [int] Délai raf. */
		const uport_refreshd='uport_refreshd';
		/** [int] Page */
		const uport_page='uport_page';
	}
}
