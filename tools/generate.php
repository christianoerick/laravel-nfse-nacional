<?php

/**
 * Lê os XSD oficiais de cada leiaute (resources/schemas/{leiaute}) e gera:
 *
 *  - resources/schema-map/{leiaute}.php: para cada tipo complexo, os filhos
 *    na ordem exigida pelo XSD (o XmlBuilder ordena o array do usuário por ele);
 *  - src/Enums/*.php: enums com rótulo para as listas fechadas (do leiaute
 *    mais novo; o que saiu dele vem do anterior);
 *  - docs/CAMPOS.md (leiaute mais novo) e docs/CAMPOS-{leiaute}.md: todos os
 *    campos da DPS e do pedido de evento, com ocorrência, tipo, opções e a
 *    descrição oficial.
 *
 * Uso: php tools/generate.php (rodar de novo a cada XSD novo).
 */

const XS = 'http://www.w3.org/2001/XMLSchema';

/** Métodos extras por enum. */
const EXTRA = [
    'Environment' => <<<'PHP'

    /** Chave do config (urls.production / urls.restricted). */
    public function key(): string
    {
        return $this === self::Production ? 'production' : 'restricted';
    }

    /** Aceita "production"/"restricted" (ou "homologation"), 1/2 ou a própria instância. */
    public static function resolve(self|string|int|null $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        return match (strtolower((string) $value)) {
            '1', 'production', 'producao', 'produção' => self::Production,
            '2', 'restricted', 'homologation', 'homologacao', 'homologação', 'sandbox' => self::Restricted,
            default => throw new \InvalidArgumentException("Ambiente desconhecido: {$value}"),
        };
    }

PHP,
];

/** Nomes de case (e rótulos que faltam no XSD) escolhidos à mão. */
const OVERRIDES = [
    'Environment' => ['1' => ['Production'], '2' => ['Restricted', 'Produção restrita (homologação)']],
    'GeneratorEnvironment' => ['1' => ['Municipality'], '2' => ['National']],
    'IssuerRole' => ['1' => ['Provider'], '2' => ['Taker'], '3' => ['Intermediary']],
    'SimplesNacionalOption' => ['1' => ['NotOptant'], '2' => ['Mei'], '3' => ['MeEpp']],
    'SimplesNacionalRegime' => ['1' => ['AllBySimples'], '2' => ['FederalBySimplesIssqnOutside'], '3' => ['AllOutsideSimples']],
    'IssqnTaxation' => ['1' => ['Taxable', 'Operação Tributável'], '2' => ['Immunity'], '3' => ['ServiceExport'], '4' => ['NonIncidence']],
    'IssqnWithholding' => ['1' => ['NotWithheld'], '2' => ['WithheldByTaker'], '3' => ['WithheldByIntermediary']],
    'TotalTaxIndicator' => ['0' => ['DoNotInform', 'Não informar (Simples Nacional / Decreto 8.264/2014)']],
    'CancellationReason' => ['1' => ['IssueError'], '2' => ['ServiceNotProvided'], '9' => ['Other']],
    'AnalysisCancellationReason' => ['1' => ['IssueError'], '2' => ['ServiceNotProvided'], '9' => ['Other', 'Outros']],
    'RejectionReason' => ['1' => ['Duplicate'], '2' => ['AlreadyIssuedByTaker'], '3' => ['NoTaxableEvent'], '4' => ['TaxResponsibilityError'], '5' => ['ValueOrServiceError'], '9' => ['Other']],
    'ReplacementReason' => ['01' => ['SimplesExclusion'], '02' => ['SimplesInclusion'], '03' => ['RetroactiveImmunityInclusion'], '04' => ['RetroactiveImmunityExclusion'], '05' => ['RejectedByTakerOrIntermediary'], '99' => ['Other']],
    'IssqnSuspension' => ['1' => ['JudicialDecision'], '2' => ['AdministrativeProceeding']],
    'EventCode' => [
        'e101101' => ['Cancellation', 'Cancelamento de NFS-e'],
        'e105102' => ['CancellationBySubstitution', 'Cancelamento de NFS-e por substituição'],
        'e105104' => ['CancellationApprovedByAnalysis', 'Cancelamento deferido por análise fiscal'],
        'e105105' => ['CancellationDeniedByAnalysis', 'Cancelamento indeferido por análise fiscal'],
        'e305101' => ['CancellationByOffice', 'Cancelamento de NFS-e por ofício'],
    ],
    'NfseStatus' => ['100' => ['Generated'], '102' => ['JudicialDecision'], '103' => ['Standalone'], '107' => ['Mei']],
    'FinalConsumer' => ['0' => ['No'], '1' => ['Yes']],
    'MdicSubmission' => ['0' => ['No'], '1' => ['Yes']],
];

$root = dirname(__DIR__);

/** Leiautes, do mais antigo para o mais novo (o último manda nos enums). */
$layouts = [
    'v1.01' => 'leiaute 1.01 (esquemas oficiais de 09/02/2026; produção até 30/11/2026)',
    'nt009' => 'NT SE/CGNFS-e 009 v1.01 (esquemas oficiais de 20/10/2026; produção restrita desde 20/10/2026 e produção a partir de 01/12/2026)',
];

$parsed = [];

foreach ($layouts as $layout => $layoutTitle) {
    $complex = [];
    $simple = [];
    $elements = [];

    foreach (glob($root."/resources/schemas/{$layout}/*.xsd") as $file) {
        if (str_contains($file, 'xmldsig')) {
            continue;
        }

        $dom = new DOMDocument;
        $dom->load($file);
        $xp = new DOMXPath($dom);
        $xp->registerNamespace('xs', XS);

        foreach ($xp->query('/xs:schema/xs:complexType') as $node) {
            $complex[$node->getAttribute('name')] = parseComplex($xp, $node, $node->getAttribute('name'), $complex);
        }

        foreach ($xp->query('/xs:schema/xs:simpleType') as $node) {
            $simple[$node->getAttribute('name')] = parseSimple($xp, $node);
        }

        foreach ($xp->query('/xs:schema/xs:element') as $node) {
            $elements[$node->getAttribute('name')] = $node->getAttribute('type');
        }
    }

    ksort($complex);
    ksort($simple);

    $map = [
        'roots' => $elements,
        'types' => array_map(fn (array $type) => [
            'attributes' => array_keys($type['attributes']),
            // [nome, tipo complexo|null, é lista, formato do valor simples|null]
            'children' => array_map(fn (array $child) => [
                $child['name'],
                isset($complex[$child['type']]) ? $child['type'] : null,
                $child['max'] !== '1',
                isset($complex[$child['type']]) ? null : valueFormat($child['type'], $simple),
            ], $type['children']),
        ], $complex),
    ];

    @mkdir($root.'/resources/schema-map', 0777, true);
    file_put_contents($root."/resources/schema-map/{$layout}.php", "<?php\n\n// Gerado por tools/generate.php a partir dos XSD oficiais ({$layoutTitle}). Não editar à mão.\n\nreturn ".exportArray($map).";\n");

    $parsed[$layout] = ['complex' => $complex, 'simple' => $simple, 'elements' => $elements, 'title' => $layoutTitle];
    echo "{$layout}: ".count($complex).' tipos complexos, '.count($simple)." simples.\n";
}

// Tipos simples para os enums: o leiaute mais novo prevalece.
$simple = array_merge(...array_column($parsed, 'simple'));

// ---------------------------------------------------------------- enums
$enums = [
    'Environment' => ['TSTipoAmbiente', 'Ambiente (tpAmb).'],
    'GeneratorEnvironment' => ['TSAmbGeradorNFSe', 'Ambiente que gerou a NFS-e (ambGer).'],
    'EmissionType' => ['TSTipoEmissao', 'Tipo de emissão da NFS-e (tpEmis).'],
    'EmissionProcess' => ['TSProcEmissao', 'Processo de emissão (procEmi).'],
    'IssuerRole' => ['TSEmitenteDPS', 'Quem emite a DPS (tpEmit).'],
    'TakerIssueReason' => ['TSMotivoEmisTI', 'Motivo da emissão pelo tomador/intermediário (cMotivoEmisTI).'],
    'CancellationReason' => ['TSCodJustCanc', 'Motivo do cancelamento (e101101/cMotivo).'],
    'AnalysisCancellationReason' => ['TSCodJustAnaliseFiscalCanc', 'Motivo da solicitação de análise fiscal para cancelamento (e101103/cMotivo).'],
    'RejectionReason' => ['TSCodMotivoRejeicao', 'Motivo da rejeição da NFS-e pelo prestador/tomador/intermediário (e202205, e203206, e204207/cMotivo).'],
    'ReplacementReason' => ['TSCodJustSubst', 'Motivo da substituição (subst/cMotivo e e105102/cMotivo).'],
    'NoNifReason' => ['TSCodNaoNIF', 'Motivo de não informar o NIF do estrangeiro (cNaoNIF).'],
    'State' => ['TSUF', 'Unidade da federação (UF).'],
    'ServiceSupplyMode' => ['TSModoPrestacao', 'Modo de prestação no comércio exterior (comExt/mdPrestacao).'],
    'ForeignTradeLink' => ['TSVincPrest', 'Vínculo entre as partes no comércio exterior (comExt/vincPrest).'],
    'ProviderSupportMechanism' => ['TSMecAFComExPrest', 'Mecanismo de apoio/fomento ao comércio exterior do prestador (comExt/mecAFComexP).'],
    'TakerSupportMechanism' => ['TSMecAFComExToma', 'Mecanismo de apoio/fomento ao comércio exterior do tomador (comExt/mecAFComexT).'],
    'TemporaryGoodsMovement' => ['TSMovTempBens', 'Vínculo com movimentação temporária de bens (comExt/movTempBens).'],
    'MdicSubmission' => ['TSEnvMDIC', 'Compartilhar com o MDIC (comExt/mdic).'],
    'VehicleCategory' => ['TSCategVeic', 'Categoria do veículo (pedágio).'],
    'TollWheelType' => ['TSRodagem', 'Tipo de rodagem (pedágio).'],
    'DeductionType' => ['TSIdeDedRed', 'Tipo de documento de dedução/redução (leiaute 1.01: docDedRed/tpDedRed).'],
    'BaseAdjustmentType' => ['TSRTCTpAjusteBC', 'Tipo de ajuste da base de cálculo (NT 009: vAjusteBC/docAjusteBC/tpAjusteBC).'],
    'IssqnSuspension' => ['TSOpExigSuspensa', 'Tipo de suspensão da exigibilidade do ISSQN (exigSusp/tpSusp).'],
    'SimplesNacionalOption' => ['TSOpSimpNac', 'Situação perante o Simples Nacional (opSimpNac).'],
    'SimplesNacionalRegime' => ['TSRegimeApuracaoSimpNac', 'Regime de apuração dos tributos pelo Simples Nacional (regApTribSN).'],
    'SpecialTaxRegime' => ['TSRegEspTrib', 'Regime especial de tributação (regEspTrib).'],
    'IssqnTaxation' => ['TSTribISSQN', 'Tributação do ISSQN (tribISSQN).'],
    'ImmunityType' => ['TSTipoImunidadeISSQN', 'Tipo de imunidade do ISSQN (tpImunidade).'],
    'IssqnWithholding' => ['TSTipoRetISSQN', 'Retenção do ISSQN (tpRetISSQN).'],
    'PisCofinsCst' => ['TSTipoCST', 'Situação tributária do PIS/COFINS (piscofins/CST).'],
    'FederalWithholding' => ['TSTipoRetPISCofins', 'Retenção de PIS/COFINS/CSLL (piscofins/tpRetPisCofins).'],
    'TotalTaxIndicator' => ['TSTipoIndTotTrib', 'Não informar o total aproximado de tributos (totTrib/indTotTrib).'],
    'MunicipalBenefitType' => ['TBMISSQN', 'Tipo do benefício municipal (BM/tpBM).'],
    'NfseStatus' => ['TStat', 'Situação da NFS-e (cStat).'],
    'EventCode' => ['TSCodigoEventoNFSe', 'Código do evento.'],
    'NfsePurpose' => ['TSRTCFinNFSe', 'Finalidade da NFS-e (finNFSe; no leiaute 1.01 fica em IBSCBS, na NT 009 no infDPS).'],
    'FinalConsumer' => ['TSRTCIndFinal', 'Operação com consumidor final (IBSCBS/indFinal).'],
    'GovernmentOperationType' => ['TSRTCTpOper', 'Tipo de operação com ente governamental (IBSCBS/tpOper).'],
    'GovernmentEntity' => ['TSRTCTpEnteGov', 'Tipo de ente governamental (IBSCBS/tpEnteGov).'],
    'RecipientIndicator' => ['TSRTCIndDest', 'Destinatário é o próprio adquirente (IBSCBS/indDest).'],
    'ReimbursementType' => ['TSRTCTpReeRepRes', 'Tipo de reembolso, repasse ou ressarcimento (IBSCBS/tpReeRepRes).'],
    'ReferencedDocumentType' => ['TSRTCTipoChaveDFe', 'Tipo de documento fiscal referenciado (tipoChaveDFe).'],
];

@mkdir($root.'/src/Enums', 0777, true);

$generatedEnums = [];

foreach ($enums as $class => [$typeName, $description]) {
    if (! isset($simple[$typeName]) || $simple[$typeName]['enum'] === []) {
        fwrite(STDERR, "aviso: {$typeName} não encontrado ou sem enumeração ({$class})\n");

        continue;
    }

    $labels = labelsFromDoc($simple[$typeName]['doc']);
    $override = OVERRIDES[$class] ?? [];
    $cases = '';
    $labelCases = '';

    $GLOBALS['__case_seen'] = [];

    foreach ($simple[$typeName]['enum'] as $value) {
        $label = $override[$value][1] ?? $labels[$value] ?? $value;
        $case = $override[$value][0] ?? caseName($label, $value);
        $GLOBALS['__case_seen'][$case] = true;
        $cases .= "    case {$case} = '".addslashes($value)."';\n";
        $labelCases .= "            self::{$case} => '".addslashes($label)."',\n";
    }

    $extra = EXTRA[$class] ?? '';

    $code = <<<PHP
<?php

namespace ChristianoErick\\NfseNacional\\Enums;

use ChristianoErick\\NfseNacional\\Enums\\Concerns\\HasOptions;

/**
 * {$description}
 *
 * Gerado por tools/generate.php a partir de {$typeName} (XSD oficial).
 */
enum {$class}: string
{
    use HasOptions;

{$cases}
    public function label(): string
    {
        return match (\$this) {
{$labelCases}        };
    }
{$extra}}

PHP;

    file_put_contents($root."/src/Enums/{$class}.php", $code);
    $generatedEnums[$typeName] = $class;
}

// ---------------------------------------------------------------- docs
@mkdir($root.'/docs', 0777, true);
$newest = array_key_last($parsed);

foreach ($parsed as $layout => $info) {
    $md = "# Mapa completo de campos — {$info['title']}\n\nGerado por `tools/generate.php` a partir dos XSD oficiais. Use os nomes das tags como chaves do array; a ordem não importa, o `XmlBuilder` segue a do XSD do leiaute em uso.\n\n";
    $md .= "Legenda da ocorrência: `1-1` obrigatório, `0-1` opcional, `0-n`/`1-n` lista. Grupos marcados com **(escolha)** aceitam só um dos filhos.\n\n";

    foreach (['DPS' => 'DPS (emissão)', 'pedRegEvento' => 'Pedido de registro de evento (cancelamento, substituição etc.)'] as $rootName => $title) {
        $md .= "## {$title}\n\n";
        $md .= "| Caminho | Ocor. | Tipo | Opções / formato | Descrição |\n|---|---|---|---|---|\n";
        $md .= walk($rootName, $info['elements'][$rootName], $info['complex'], $info['simple'], $generatedEnums, 0, []);
        $md .= "\n";
    }

    file_put_contents($root.'/docs/'.($layout === $newest ? 'CAMPOS.md' : "CAMPOS-{$layout}.md"), $md);
}

echo count($generatedEnums)." enums.\n";

// ======================================================================

function parseComplex(DOMXPath $xp, DOMElement $node, string $name, array &$complex): array
{
    $type = ['attributes' => [], 'children' => [], 'doc' => doc($xp, $node)];

    foreach ($xp->query('./xs:attribute | ./xs:simpleContent/xs:extension/xs:attribute', $node) as $attr) {
        $type['attributes'][$attr->getAttribute('name')] = [
            'type' => $attr->getAttribute('type'),
            'required' => $attr->getAttribute('use') === 'required',
        ];
    }

    $content = $xp->query('./xs:sequence | ./xs:choice | ./xs:all', $node)->item(0);

    if ($content instanceof DOMElement) {
        collectChildren($xp, $content, $name, $type['children'], $complex, null);
    }

    return $type;
}

/**
 * Filhos em ordem de documento, achatando sequence/choice. Filhos de um
 * choice ficam marcados com o id do grupo (só para a documentação).
 */
function collectChildren(DOMXPath $xp, DOMElement $group, string $owner, array &$children, array &$complex, ?string $choice, bool $optionalGroup = false): void
{
    static $choiceId = 0;

    foreach ($group->childNodes as $child) {
        if (! $child instanceof DOMElement) {
            continue;
        }

        $groupOptional = $optionalGroup || $group->getAttribute('minOccurs') === '0';
        $groupRepeats = $group->hasAttribute('maxOccurs') && $group->getAttribute('maxOccurs') !== '1';

        if ($child->localName === 'element') {
            $name = $child->getAttribute('name') ?: $child->getAttribute('ref');
            $typeName = $child->getAttribute('type');

            if ($name === 'ds:Signature' || $child->getAttribute('ref') === 'ds:Signature') {
                $name = 'Signature';
                $typeName = 'ds:Signature';
            }

            $inline = $xp->query('./xs:complexType', $child)->item(0);
            $inlineSimple = $xp->query('./xs:simpleType', $child)->item(0);

            if ($inline instanceof DOMElement) {
                $typeName = $owner.'_'.$name;
                $complex[$typeName] = parseComplex($xp, $inline, $typeName, $complex);
            } elseif ($inlineSimple instanceof DOMElement) {
                $typeName = $owner.'_'.$name;
                $GLOBALS['simple'][$typeName] = parseSimple($xp, $inlineSimple);
            }

            $children[] = [
                'name' => $name,
                'type' => $typeName,
                'min' => ($groupOptional || $choice !== null) ? '0' : ($child->hasAttribute('minOccurs') ? $child->getAttribute('minOccurs') : '1'),
                'max' => $child->hasAttribute('maxOccurs') ? $child->getAttribute('maxOccurs') : '1',
                'choice' => $choice,
                'doc' => doc($xp, $child),
            ];
        } elseif (in_array($child->localName, ['sequence', 'choice', 'all'], true)) {
            $nextChoice = $child->localName === 'choice' ? 'c'.(++$choiceId) : $choice;
            collectChildren($xp, $child, $owner, $children, $complex, $nextChoice, $groupOptional);
        }
    }
}

/**
 * Formato para converter DateTime/float do usuário: datetime, date ou
 * decimal:N (casas). Null = texto livre.
 */
function valueFormat(string $type, array $simple): ?string
{
    $base = $simple[$type]['base'] ?? $type;

    return match (true) {
        $type === 'TSDateTimeUTC' || $base === 'xs:dateTime' => 'datetime',
        $type === 'TSData' || $type === 'xs:date' || $base === 'xs:date' => 'date',
        (bool) preg_match('/^TSDec\d+V(\d+)$/', $type, $m) => 'decimal:'.$m[1],
        default => null,
    };
}

function parseSimple(DOMXPath $xp, DOMElement $node): array
{
    $restriction = $xp->query('./xs:restriction', $node)->item(0);
    $facets = [];
    $enum = [];

    if ($restriction instanceof DOMElement) {
        foreach ($restriction->childNodes as $facet) {
            if (! $facet instanceof DOMElement) {
                continue;
            }

            if ($facet->localName === 'enumeration') {
                $enum[] = $facet->getAttribute('value');
            } elseif ($facet->localName !== 'whiteSpace') {
                $facets[$facet->localName] = $facet->getAttribute('value');
            }
        }
    }

    return [
        'base' => $restriction?->getAttribute('base') ?? '',
        'enum' => $enum,
        'facets' => $facets,
        'doc' => doc($xp, $node),
    ];
}

function doc(DOMXPath $xp, DOMElement $node): string
{
    $doc = $xp->query('./xs:annotation/xs:documentation', $node)->item(0);

    return $doc ? trim(preg_replace('/[ \t]+/', ' ', $doc->textContent)) : '';
}

/**
 * "1 - Produção; 2 - Homologação" → ['1' => 'Produção', '2' => 'Homologação'].
 */
function labelsFromDoc(string $doc): array
{
    $labels = [];

    preg_match_all('/(?:^|[\n;:])\s*([0-9A-Za-z]{1,6})\s*[-–=]\s*([^;\n]+)/u', $doc, $matches, PREG_SET_ORDER);

    foreach ($matches as [, $value, $label]) {
        $labels[$value] ??= rtrim(trim($label), '.;');
    }

    return $labels;
}

function caseName(string $label, string $value): string
{
    $ascii = strtr($label, [
        'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'é' => 'e', 'ê' => 'e', 'è' => 'e', 'í' => 'i', 'î' => 'i',
        'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ç' => 'c', 'ñ' => 'n',
        'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'É' => 'E', 'Ê' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
        'Ú' => 'U', 'Ç' => 'C', 'º' => '', 'ª' => '', '≠' => ' ',
    ]);
    $words = preg_split('/[^A-Za-z0-9]+/', $ascii, -1, PREG_SPLIT_NO_EMPTY);
    $words = array_slice($words, 0, 6);
    $name = implode('', array_map(fn ($w) => ucfirst(strtolower($w)), $words));

    if ($name === '' || ctype_digit($name[0])) {
        $name = 'Code'.preg_replace('/\W/', '', $value).$name;
    }

    return $name.(isset($GLOBALS['__case_seen'][$name]) ? $value : '');
}

function walk(string $path, string $typeName, array $complex, array $simple, array $enums, int $depth, array $stack): string
{
    if ($depth > 14 || in_array($typeName, $stack, true)) {
        return '';
    }

    $rows = '';
    $type = $complex[$typeName] ?? null;

    if ($type === null) {
        return '';
    }

    foreach ($type['attributes'] as $name => $attribute) {
        $rows .= row($path.'/@'.$name, $attribute['required'] ? '1-1' : '0-1', $attribute['type'], $simple, $enums, '');
    }

    foreach ($type['children'] as $child) {
        if ($child['name'] === 'Signature') {
            $rows .= "| `{$path}/Signature` | ".occ($child)." | XMLDSig | gerada pelo pacote | Assinatura digital |\n";

            continue;
        }

        $childPath = $path.'/'.$child['name'];
        $doc = $child['doc'].($child['choice'] ? ' **(escolha)**' : '');

        if (isset($complex[$child['type']])) {
            $rows .= "| **`{$childPath}`** | ".occ($child).' | grupo | | '.cell($doc)." |\n";
            $rows .= walk($childPath, $child['type'], $complex, $simple, $enums, $depth + 1, [...$stack, $typeName]);
        } else {
            $rows .= row($childPath, occ($child), $child['type'], $simple, $enums, $doc);
        }
    }

    return $rows;
}

function occ(array $child): string
{
    return $child['min'].'-'.($child['max'] === 'unbounded' ? 'n' : $child['max']);
}

function row(string $path, string $occ, string $typeName, array $simple, array $enums, string $doc): string
{
    $info = $simple[$typeName] ?? null;
    $options = '';

    if ($info !== null && $info['enum'] !== []) {
        $labels = labelsFromDoc($info['doc']);
        $options = implode('<br>', array_map(fn ($v) => '`'.$v.'`'.(isset($labels[$v]) ? ' '.$labels[$v] : ''), $info['enum']));

        if (isset($enums[$typeName])) {
            $options .= '<br>→ `Enums\\'.$enums[$typeName].'`';
        }
    } elseif ($info !== null) {
        $options = implode(', ', array_map(fn ($k, $v) => $k.'='.$v, array_keys($info['facets']), $info['facets']));
    }

    return '| `'.$path.'` | '.$occ.' | '.$typeName.' | '.cell($options).' | '.cell($doc).' |'."\n";
}

function cell(string $text): string
{
    $text = preg_replace('/\s*\n\s*/', ' ', trim($text));

    return str_replace('|', '\\|', mb_strimwidth($text, 0, 600, '…'));
}

function exportArray(array $value, int $indent = 0): string
{
    $pad = str_repeat('    ', $indent + 1);
    $isList = array_is_list($value);
    $lines = [];

    foreach ($value as $key => $item) {
        $prefix = $isList ? '' : var_export($key, true).' => ';
        $lines[] = $pad.$prefix.(is_array($item) ? exportArray($item, $indent + 1) : var_export($item, true));
    }

    if ($lines === []) {
        return '[]';
    }

    // Listas curtas de escalares numa linha só.
    if ($isList && count($value) <= 4 && ! array_filter($value, 'is_array')) {
        return '['.implode(', ', array_map(fn ($v) => var_export($v, true), $value)).']';
    }

    return "[\n".implode(",\n", $lines).",\n".str_repeat('    ', $indent).']';
}
