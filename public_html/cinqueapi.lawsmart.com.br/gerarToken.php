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
$signingKey   = InMemory::plainText('f25661dbf0f02deed530d561f411f622157fe8f675be98a7a3e955f7eed06011');
// $signingKey = InMemory::base64Encoded(
//     'hiG8DlOKvtih6AxlZn5XKImZ06yu8I3mkOzaJrEuW8yAv8Jnkw330uMt8AEqQ5LB'
// );
$now   = new DateTimeImmutable();
$token = $tokenBuilder
    // Configures the issuer (iss claim)
    ->issuedBy('https://cinqueapi.lawsmart.com.br')
    // Configures the audience (aud claim)
    // ->permittedFor('http://example.org')
    // Configures the subject of the token (sub claim)
    ->relatedTo('lawsmart')
    // Configures the id (jti claim)
    ->identifiedBy('129e39a852c915883a1bc4d6f2dcc6a430ca91e7a9da0f32aac9b4ff3332c6f4')
    // Configures the time that the token was issue (iat claim)
    ->issuedAt($now)
    // Configures the time that the token can be used (nbf claim)
    ->canOnlyBeUsedAfter($now->modify('+5 minute'))
    // Configures the expiration time of the token (exp claim)
    ->expiresAt($now->modify('+5 minute'))
    // Configures a new claim, called "uid"
    ->withClaim('uid', 1)
    // Configures a new header, called "foo"
    ->withHeader('client_id', 'f25661dbf0f02deed530d561f411f622157fe8f675be98a7a3e955f7eed06011')
    // Builds a new token
    ->getToken($algorithm, $signingKey);

echo $token->toString();