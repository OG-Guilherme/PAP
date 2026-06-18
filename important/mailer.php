<?php
// important/mailer.php
// Configuração do PHPMailer para envio de emails via Gmail SMTP
//
// INSTALAÇÃO:
//   No terminal, dentro da pasta do projeto:
//   composer require phpmailer/phpmailer
//
// CONFIGURAÇÃO GMAIL:
//   As credenciais SMTP ficam guardadas na tabela `configuracoes_email`
//   da base de dados (ver configuracoes_email.sql) — não há nada a editar
//   neste ficheiro. Para definir/alterar as credenciais, corre no phpMyAdmin:
//
//     UPDATE configuracoes_email SET
//       smtp_user = 'o.teu.email@gmail.com',
//       smtp_pass = 'xxxx xxxx xxxx xxxx',
//       mail_from = 'o.teu.email@gmail.com'
//     WHERE id = 1;
//
//   1. Vai a https://myaccount.google.com/apppasswords
//   2. Cria uma "App Password" para "Mail"
//   3. Usa essa password de 16 caracteres em smtp_pass
//   (NÃO uses a tua password normal do Gmail)

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Lê as configurações de email guardadas na tabela `configuracoes_email`.
 *
 * @return array|null Configurações (associativo), ou null se não existirem.
 */
function getMailConfig(PDO $pdo): ?array
{
    try {
        $stmt   = $pdo->query("SELECT * FROM configuracoes_email ORDER BY id LIMIT 1");
        $config = $stmt->fetch();
        return $config ?: null;
    } catch (\PDOException $e) {
        return null;
    }
}

/**
 * Envia um email.
 *
 * @param PDO    $pdo
 * @param string $to      Destinatário
 * @param string $subject Assunto
 * @param string $body    Corpo HTML
 * @return true|string    true se OK, mensagem de erro se falhar
 */
function sendMail(PDO $pdo, string $to, string $subject, string $body): true|string
{
    $config = getMailConfig($pdo);

    if (!$config) {
        return 'Não foi possível ler as configurações de email da tabela `configuracoes_email` (tabela vazia ou inexistente — corre o configuracoes_email.sql).';
    }

    // Verificação rápida: avisa logo se ainda estão os valores de exemplo
    if ($config['smtp_user'] === 'o.teu.email@gmail.com' || stripos($config['smtp_pass'], 'xxxx') !== false) {
        return 'Credenciais do Gmail ainda não configuradas na tabela `configuracoes_email` (smtp_user / smtp_pass têm os valores de exemplo). Atualiza essa linha no phpMyAdmin.';
    }

    $mail     = new PHPMailer(true);
    $debugLog = '';

    try {
        // Servidor
        $mail->isSMTP();
        $mail->Host       = $config['smtp_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['smtp_user'];
        $mail->Password   = $config['smtp_pass'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int) $config['smtp_port'];
        $mail->CharSet    = 'UTF-8';

        // Captura o diálogo SMTP para diagnóstico em caso de falha
        // (o PHPMailer oculta automaticamente credenciais nesta saída)
        $mail->SMTPDebug   = SMTP::DEBUG_SERVER;
        $mail->Debugoutput = function (string $str, int $level) use (&$debugLog) {
            $debugLog .= trim($str) . ' || ';
        };

        // Remetente e destinatário
        $mail->setFrom($config['mail_from'], $config['mail_from_name']);
        $mail->addAddress($to);

        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '</p>'], "\n", $body));

        $mail->send();
        return true;
    } catch (Exception $e) {
        $erro = $mail->ErrorInfo ?: $e->getMessage();
        if ($debugLog !== '') {
            $erro .= ' | SMTP: ' . $debugLog;
        }
        return $erro;
    }
}

/**
 * Gera o template HTML do email de recuperação de password.
 */
function emailRecuperacaoTemplate(string $nome, string $link): string
{
    $siteName = defined('SITE_NAME') ? SITE_NAME : 'EduWeb';
    $ano      = date('Y');
    return <<<HTML
<!DOCTYPE html>
<html lang="pt">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:40px 0;">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
        <!-- Header -->
        <tr>
          <td style="background:#1e3c72;padding:28px 36px;">
            <p style="margin:0;color:white;font-size:22px;font-weight:bold;">{$siteName}</p>
            <p style="margin:4px 0 0;color:rgba(255,255,255,0.7);font-size:13px;">Recuperação de Password</p>
          </td>
        </tr>
        <!-- Body -->
        <tr>
          <td style="padding:36px;">
            <p style="margin:0 0 16px;font-size:16px;color:#333;">Olá, <strong>{$nome}</strong>!</p>
            <p style="margin:0 0 24px;font-size:14px;color:#555;line-height:1.6;">
              Recebemos um pedido para recuperar a password da tua conta no {$siteName}.<br>
              Clica no botão abaixo para definires uma nova password. O link é válido durante <strong>1 hora</strong>.
            </p>
            <!-- Botão -->
            <table cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
              <tr>
                <td style="background:#1e3c72;border-radius:8px;padding:14px 28px;">
                  <a href="{$link}" style="color:white;text-decoration:none;font-size:15px;font-weight:bold;">
                    Redefinir Password
                  </a>
                </td>
              </tr>
            </table>
            <p style="margin:0 0 8px;font-size:13px;color:#888;">
              Se o botão não funcionar, copia e cola este link no browser:
            </p>
            <p style="margin:0 0 28px;font-size:12px;color:#1e3c72;word-break:break-all;">{$link}</p>
            <hr style="border:none;border-top:1px solid #eee;margin:0 0 20px;">
            <p style="margin:0;font-size:12px;color:#aaa;line-height:1.5;">
              Se não pediste a recuperação de password, ignora este email — a tua conta está segura.<br>
              Este link expira automaticamente dentro de 1 hora.
            </p>
          </td>
        </tr>
        <!-- Footer -->
        <tr>
          <td style="background:#f8f8f8;padding:16px 36px;border-top:1px solid #eee;">
            <p style="margin:0;font-size:12px;color:#bbb;text-align:center;">&copy; {$ano} {$siteName}. Todos os direitos reservados.</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}