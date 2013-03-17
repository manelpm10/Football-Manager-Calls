<?php
/**
 * Mail class.
 *
 * @author Manel Pérez
 */

/**
 * Class Mail.
 *
 * Core Mail class.
 */
class Mail
{
	/**
	 * Subject of mail.
	 *
	 * @var string.
	 */
	protected $subject = '';

	/**
	 * Body of mail.
	 *
	 * @var string.
	 */
	protected $body = '';

	/**
	 * Email from this email.
	 *
	 * @var string.
	 */
	protected $from = '';

	/**
	 * Array with emails who receive the email.
	 *
	 * @var array.
	 */
	protected $receiver = array();

	/**
	 * Carbon copy emails who receive the email.
	 *
	 * @var array.
	 */
	protected $carbon_copy = array();

	/**
	 * Reply to these email.
	 *
	 * @var string.
	 */
	protected $reply_to = '';

	/**
	 * Content type for the email.
	 *
	 * @var array.
	 */
	protected $content_type = '';

	/**
	 * Sets the subject for the e-mail.
	 *
	 * @param string $subject Subject for the email.
	 * @return void;
	 */
	public function setSubject( $subject )
	{
		$this->subject = SUBJECT_TAG . " $subject";
	}

	/**
	 * Sets the body message for the e-mail.
	 *
	 * @param string $body Body message for the email.
	 * @return void
	 */
	public function setBody( $body )
	{
		$this->body = $body;
	}

	/**
	 * Sets the recipient for the e-mail.
	 *
	 * @param string $receiver Recipient for the e-mail.
	 * @return void
	 */
	public function setReceiver( $receiver )
	{
		$this->receiver[] = $receiver;
	}

	/**
	 * Sets the recipient for the e-mail.
	 *
	 * @param string $receiver Recipient for the e-mail.
	 * @return void
	 */
	public function resetReceiver()
	{
		$this->receiver = array();
	}

	/**
	 * Sets the sender for the e-mail.
	 *
	 * @param string $from Sender for the e-mail.
	 * @return void
	 */
	public function setFrom( $from )
	{
		$this->from = $from;
	}

	/**
	 * Sets a Carbon Copy recipient.
	 *
	 * @param string $carbon_copy Carbon copy recipient.
	 * @return void
	 */
	public function setCc( $carbon_copy )
	{
		$this->carbon_copy[] = $carbon_copy;
	}

	/**
	 * Sets the reply to email.
	 *
	 * @param string $reply_to Reply to recipient.
	 * @return void
	 */
	public function setReplyTo( $reply_to )
	{
		$this->reply_to = $reply_to;
	}

	/**
	 * Sets the Content Type and charset for the message.
	 *
	 * @param string $type Content Type for the message.
	 * @param string $charset Charset for the e-mail.
	 * @return void
	 */
	public function setContentType( $type, $charset )
	{
		$this->content_type = "Content-Type: $type; charset=$charset\r\n";
	}

	/**
	 * Builds the headers with all the specified data by the setters.
	 *
	 * @return string
	 */
	protected function buildHeaders()
	{
		if ( !empty( $this->from ) )
		{
			$headers = "From: {$this->from}\r\n";
		}

		if ( !empty( $this->reply_to ) )
		{
			$headers .= "Reply-To: {$this->reply_to}\r\n";
		}

		if ( !empty( $this->carbon_copy ) )
		{
			$carbon_copy = implode( ',', $this->carbon_copy );
			$headers .= "Cc: {$carbon_copy}\r\n";
		}

		if ( !empty( $this->content_type ) )
		{
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= $this->content_type;
		}

		return $headers;
	}

	/**
	 * Sends the constructed e-mail.
	 *
	 * @return void
	 */
	public function send()
	{
		if ( empty( $this->subject ) || empty( $this->body ) || empty( $this->receiver ) )
		{
			throw new MailException( 'You must declare a subject, message and receiver' );
		}

		$receiver = implode( ',', $this->receiver );
		mail( $receiver, $this->subject, $this->body, $this->buildHeaders() );
	}
}

/**
 * MailException class.
 *
 * Exception class for the Mail class.
 */
class MailException extends CustomException
{

}
