<?php

return [
  // tenat website default menus 
  'menus' => [
    [
      "text" => "Inicio",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "home",
    ],
    [
      "text" => "Propiedades",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "properties",
    ],
    [
      "text" => "Proyectos",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "projects",
    ],
    [
      "text" => "Equipo",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "agents",
    ],

    [
      "text" => "Blog",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "blog",
    ],
    [
      "text" => "Nosotros",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "about-us",
    ],
    [
      "text" => "FAQ",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "faq",
    ],
    [
      "text" => "Contacto",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "contact",
    ]

  ],


  // child menus example
  // [
  //   "text" => "Shop",
  //   "href" => "",
  //   "icon" => "empty",
  //   "target" => "_self",
  //   "title" => "",
  //   "type" => "custom",
  //   "children" => [
  //     [
  //       "text" => "Products",
  //       "href" => "",
  //       "icon" => "empty",
  //       "target" => "_self",
  //       "title" => "",
  //       "type" => "products",
  //     ],
  //     [
  //       "text" => "Cart",
  //       "href" => "",
  //       "icon" => "empty",
  //       "target" => "_self",
  //       "title" => "",
  //       "type" => "cart",
  //     ],
  //   ],
  // ],
  // tenant default Daynamic Mail Templates
  'mailTemplates' => [

    [
      'mail_type' => "verify_email",
      'mail_subject' => "Verifica tu E-mail",
      'mail_body' => "<p>Hola <b>{username}</b>,</p><p>Necesitamos verificar tu e-mail antes de que puedas acceder a tu cuenta.</p><p>Verifica tu email Aqui, {verification_link}.</p><p>Gracias por suscribirte.<br>{website_title}</p>",
    ],
    [
      'mail_type' => "reset_password",
      'mail_subject' => "Recuperá la contraseña de tu cuenta",
      'mail_body' => '<p>Hola {customer_name},</p><p>Recibimos una solicitud para restablecer tu contraseña. Si no reconoces esta solicitud, ignora este mensaje. De otro modo, si querés continuar y restablecer tu contraseña, ve al siguiente enlace:.</p><p>{password_reset_link}</p><p>Saludos cordiales,<br>{website_title}</p>',

    ],

    // [
    //   'mail_type' => "payment_success",
    //   'mail_subject' => "Payment Success",
    //   'mail_body' => '<p>Hi {customer_name},</p><p>Your payment is completed. We have attached an invoice in this mail.<br />Invoice No: #{invoice_number}</p><p>Best regards.<br />{website_title}</p>',
    // ],
    // [
    //   'mail_type' => "payment_approved",
    //   'mail_subject' => "Payment Approved",
    //   'mail_body' => '<p>Hi {customer_name},</p><p>We have approved your payment. We have attached an invoice in this mail. You can see the details here.<br />Invoice No: #{invoice_number}</p><p>Best regards.<br />{website_title}</p>',
    // ],
    // [
    //   'mail_type' => "payment_rejected",
    //   'mail_subject' => "Payment Rejected",
    //   'mail_body' => '<p>Hi {customer_name},</p><p>We have rejected your payment. We have attached an invoice in this mail.</p><p>For any kind of query please contact us.</p><p>Support email: admin@gmail.com<br />Invoice No: #{invoice_number}</p><p>Best regards.<br />{website_title}</p>',
    // ],
    [
      'mail_type' => "agent_register",
      'mail_subject' => "Nueva cuenta de agente creada",
      'mail_body' => '<p>Hola {username},<br /><br />Te damos la bienvenida, esta es una confirmación nuestra sobre la creación de tu cuenta.<br /><br />Acá te detallamos los datos para acceder a tu cuenta:</p>
<p><strong>Link: </strong>{login_url}<br /><strong>Usuario:</strong> {username}<br /><strong>Contraseña:</strong> {password}<br /><br /></p>
<p>Saludos cordiales,<br />{website_title}.</p>',
    ],

  ]
];
