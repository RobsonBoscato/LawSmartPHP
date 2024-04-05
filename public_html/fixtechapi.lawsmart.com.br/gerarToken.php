<?php
declare(strict_types=1);

use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token\Builder;

require 'vendor/autoload.php';

$tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
$algorithm    = new Sha256();
$signingKey   = InMemory::plainText('a2696c2f90ad47ab13b22a8786aab9682a2b164018257ff5eb62d5cc5e14c277');
// $signingKey = InMemory::base64Encoded(
//     'hiG8DlOKvtih6AxlZn5XKImZ06yu8I3mkOzaJrEuW8yAv8Jnkw330uMt8AEqQ5LB'
// );
$now   = new DateTimeImmutable();
$token = $tokenBuilder
    // Configures the issuer (iss claim)
    ->issuedBy('http://fixtechapi.lawsmart.com.br')
    // Configures the audience (aud claim)
    // ->permittedFor('http://example.org')
    // Configures the subject of the token (sub claim)
    ->relatedTo('lawsmart')
    // Configures the id (jti claim)
    ->identifiedBy('7bcb4f818516a747ca159459dccb09c2873a1c5e8482b69ed3688b7493b9f0ac')
    // Configures the time that the token was issue (iat claim)
    ->issuedAt($now)
    // Configures the time that the token can be used (nbf claim)
    ->canOnlyBeUsedAfter($now->modify('+5 minute'))
    // Configures the expiration time of the token (exp claim)
    ->expiresAt($now->modify('+5 minute'))
    // Configures a new claim, called "uid"
    ->withClaim('uid', 1)
    // Configures a new header, called "foo"
    ->withHeader('client_id', '9454q54q5e4w5562e5265w6d1wdwd2656w516w1d')
    // Builds a new token
    ->getToken($algorithm, $signingKey);

echo $token->toString();