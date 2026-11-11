# Laravel NFS-e Nacional

Emissão de **NFS-e pelo Sistema Nacional (Sefin Nacional / ADN)** em Laravel 12 e 13, sem bibliotecas de terceiros para XML ou assinatura.

- **DPS completa** no leiaute oficial **1.01**. Todos os campos do XSD são aceitos pelos nomes oficiais das tags, inclusive IBS/CBS, comércio exterior, obra, evento, deduções e benefícios. A ordem dos campos é tirada do XSD, e a DPS é validada localmente antes do envio.
- **Assinatura XMLDSig** com certificado A1 (.pfx), em RSA-SHA1 (padrão do manual) ou RSA-SHA256. Abre também .pfx antigo com cifra RC2.
- **Emissão segura contra duplicidade.** `emitOrRecover()` consulta a DPS antes de reenviar.
- **Consultas e eventos:** NFS-e por chave, DPS por Id, cancelamento (e101101), cancelamento por substituição (e105102) e qualquer outro evento do leiaute.
- **ADN:** DANFSe oficial em PDF, parâmetros municipais (convênio, alíquotas, regimes, retenções, benefícios) e distribuição por NSU.
- **DANFSe v2.0 gerado localmente**, no mesmo leiaute do Sistema Nacional, com QR Code.
- **Enums com rótulo** para todas as listas do leiaute, prontos para `<select>`. Inclui também a lista nacional de serviços (cTribNac) e os códigos de rejeição do Anexo I.
- **mTLS com verificação SSL ligada.** O certificado vai para arquivos temporários 0600, apagados ao fim.

## Instalação

```bash
composer require christianoerick/laravel-nfse-nacional
php artisan vendor:publish --tag=nfse-nacional-config   # opcional
```

Requisitos: PHP 8.2+, Laravel 12 ou 13 e as extensões `openssl`, `dom`, `libxml`, `zlib` e `gd`.

```dotenv
NFSE_NACIONAL_ENVIRONMENT=production   # ou restricted (produção restrita)
NFSE_NACIONAL_APP_VERSION=MeuSistema-1.0
NFSE_NACIONAL_SIGNATURE=sha1           # ou sha256
```

## Certificado

```php
use ChristianoErick\NfseNacional\Facades\NfseNacional;

$certificado = NfseNacional::certificate(file_get_contents($caminhoPfx), $senha);

$certificado->document();             // "11222333000181"
$certificado->holderName();           // "EMPRESA LTDA"
$certificado->validTo();              // CarbonImmutable
$certificado->daysUntilExpiration();  // 351
$certificado->toPfx($senha);          // regrava em AES-256 (útil para .pfx com cifra antiga)
```

```bash
php artisan nfse-nacional:certificate storage/app/private/cert.pfx
```

## Emitir

```php
use ChristianoErick\NfseNacional\Dps\Dps;
use ChristianoErick\NfseNacional\Enums\IssqnWithholding;
use ChristianoErick\NfseNacional\Facades\NfseNacional;

$dps = Dps::make([
    'serie' => 1,                 // aplicativo próprio: 1 a 49999
    'nDPS' => 15,                 // sequencial por série; nunca repita
    'dCompet' => now(),
    'cLocEmi' => '2704302',       // IBGE do município emissor
    'prest' => [
        'CNPJ' => '11222333000181',
        'fone' => '82999990000',
        'email' => 'financeiro@empresa.com.br',
        'regTrib' => ['opSimpNac' => 3, 'regApTribSN' => 1, 'regEspTrib' => 0],
    ],
    'toma' => [
        'CNPJ' => '11444777000161',
        'xNome' => 'CLIENTE LTDA',
        'end' => [
            'endNac' => ['cMun' => '2704302', 'CEP' => '57000000'],
            'xLgr' => 'Rua Exemplo', 'nro' => '100', 'xBairro' => 'Centro',
        ],
    ],
    'serv' => [
        'locPrest' => ['cLocPrestacao' => '2704302'],
        'cServ' => ['cTribNac' => '010501', 'cTribMun' => '001', 'xDescServ' => 'Licenciamento de software.'],
    ],
    'valores' => [
        'vServPrest' => ['vServ' => 7500],
        'trib' => [
            'tribMun' => ['tribISSQN' => 1, 'tpRetISSQN' => IssqnWithholding::NotWithheld],
            'tribFed' => ['piscofins' => ['CST' => '00', 'tpRetPisCofins' => 0]],
            'totTrib' => ['pTotTribSN' => 6.00],
        ],
    ],
]);

$resultado = NfseNacional::client($certificado)->emitOrRecover($dps);

$resultado->accessKey();      // chave de 50 dígitos
$resultado->number();         // número da NFS-e
$resultado->xml();            // XML da NFS-e (guarde!)
$resultado->nfse->summary();  // dados prontos para gravar
```

O pacote preenche sozinho `tpAmb`, `verAplic`, `tpEmit = 1` e `dhEmi` (um minuto antes de agora, porque o Sefin recusa `dhEmi` no futuro). Também calcula o `Id` da DPS. Valores aceitam número, string, enum, `DateTimeInterface` e `bool`.

**Todos os campos possíveis** estão em [`docs/CAMPOS.md`](docs/CAMPOS.md), gerado do XSD oficial, com ocorrência, formato, opções e descrição.

### Erros

| Exceção | Quando | O que fazer |
|---|---|---|
| `InvalidDataException` | campo que não existe no leiaute (a mensagem lista os aceitos) | corrigir o array |
| `SchemaValidationException` | a DPS não passa no XSD; nada foi enviado | `$e->errors` |
| `RejectedException` | o Sefin recusou | `$e->codes()` (ex.: `['E0014']`) e `$e->messages` |
| `ConnectionException` | rede, timeout, 429 ou 5xx | tentar de novo com `emitOrRecover()` |
| `NotFoundException` | consulta sem resultado | — |

`ChristianoErick\NfseNacional\Codes\ErrorCodes::message('E0014')` devolve a mensagem oficial do código.

## Duplicar uma nota

```php
$original = NfseNacional::client($certificado)->get($chave);   // ou NfseDocument::fromXml($xmlGuardado)

$nova = $original->dps()
    ->duplicate(series: 1, number: $proximoNumero)   // tira dhEmi/verAplic/tpAmb/subst
    ->with('dCompet', now())
    ->with('valores.vServPrest.vServ', 8200)
    ->with('toma.CNPJ', '11444777000161');

NfseNacional::client($certificado)->emitOrRecover($nova);
```

Funciona também com nota emitida no portal. A DPS dela usa a série 70000 (Emissor Nacional web), e `duplicate()` troca pela série do seu aplicativo.

## Cancelar e substituir

```php
use ChristianoErick\NfseNacional\Enums\CancellationReason;
use ChristianoErick\NfseNacional\Enums\ReplacementReason;

$client->cancel($chave, CancellationReason::IssueError, 'Valor informado errado na emissão.');

// Substituição: a nova DPS leva o grupo subst. O Sefin gera a nova e cancela a original.
$client->emitOrRecover($nova->with('subst', [
    'chSubstda' => $chaveOriginal,
    'cMotivo' => ReplacementReason::Other,
    'xMotivo' => 'Correção do valor do serviço.',
]));

// Município que limita o cancelamento direto (E0823): pedido de análise fiscal (e101103).
$client->registerEvent(EventRequest::analysisCancellation($chave, AnalysisCancellationReason::IssueError, 'Nota emitida com valor errado.'));

// Catálogo para a tela "Registrar evento" (evento, motivos, justificativa):
AuthorEvents::all('provider');   // e101101, e101103, e202201, e202205
$client->registerEvent(EventRequest::fromCode($chave, 'e202205', '9', 'Justificativa...'));

// Qualquer outro evento, pelos nomes oficiais:
$client->registerEvent(EventRequest::make($chave, 'e202201', [...]));
```

## Consultas

```php
$client->dpsExists($dpsId);          // HEAD /dps/{id}
$client->accessKeyForDps($dpsId);    // GET /dps/{id}
$client->find($chave);               // NfseDocument|null
$client->event($chave, '101101');    // evento por tipo/sequência
$client->events($chave);             // todos os eventos (ADN)
$client->distribution($nsu);         // documentos por NSU (ADN)
```

## Parâmetros municipais

```php
$client->municipalAgreement('2704302')->usesNationalIssuer();   // dá para emitir pelo Sefin?
$client->municipalRate('2704302', '01.05.01.001', '2026-11-01');
$client->specialRegimes('2704302', '01.05.01.001', '2026-11-01');
$client->withholdings('2704302', '2026-11-01');
```

```bash
php artisan nfse-nacional:municipality 2704302 --pfx=storage/app/private/cert.pfx
```

## DANFSe

```php
$pdf = $client->danfse($chave);                                  // PDF oficial (ADN)

$pdf = NfseNacional::danfse()                                    // gerado localmente, mesmo leiaute
    ->resolveCityUsing(fn (string $ibge) => Municipio::nome($ibge))   // opcional
    ->watermark($cancelada ? 'CANCELADA' : null)
    ->render(NfseDocument::fromXml($xml));
```

Para personalizar o template: `php artisan vendor:publish --tag=nfse-nacional-views`.

## Listas para formulários

```php
use ChristianoErick\NfseNacional\Enums\SimplesNacionalOption;

SimplesNacionalOption::options();   // [['value' => '1', 'label' => 'Não Optante'], ...]
NfseNacional::services()->search('software');
NfseNacional::services()->options();
```

São 39 enums, um para cada lista fechada do leiaute: regimes, tributação e retenção do ISSQN, CST de PIS/COFINS, imunidades, motivos de cancelamento e substituição, comércio exterior, IBS/CBS, UF e outras.

## Ambiente de testes

Use `NFSE_NACIONAL_ENVIRONMENT=restricted`, ou passe o ambiente por chamada: `NfseNacional::client($certificado, 'restricted')`.

## Sobre os XSD

Os esquemas oficiais (leiaute 1.01) ficam em `resources/schemas`. Há um ajuste: o padrão de `TSSerieDPS` vem como `^0{0,4}\d{1,5}$`, mas em XSD `^` e `$` são literais, então o libxml recusaria qualquer série. Na nossa cópia, o padrão é `0{0,4}\d{1,5}`.

Quando sair um XSD novo, coloque-o em `resources/schemas` e rode `composer generate`. Isso regera o mapa de ordem dos campos, os enums e o `docs/CAMPOS.md`.

## Testes

```bash
composer test
```

## Licença

MIT.
