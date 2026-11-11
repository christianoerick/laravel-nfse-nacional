<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>DANFSe {{ $d['number'] }}</title>
<style>
    @page { margin: 18px 22px; }
    * { box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 6.9pt; color: #000; margin: 0; }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    td { vertical-align: top; padding: 1px 3px 2px; }
    .frame { border: 1px solid #000; }
    .label { font-weight: bold; font-size: 6pt; display: block; }
    .value { font-size: 6.9pt; display: block; word-wrap: break-word; }
    .section { border-top: 1px solid #000; }
    .section-title { font-weight: bold; font-size: 7.2pt; background: #efefef; }
    .shade { background: #efefef; }
    .center { text-align: center; }
    .header td { vertical-align: middle; }
    .title { font-size: 9.5pt; font-weight: bold; text-align: center; }
    .muted { font-size: 5.8pt; }
    .key { font-size: 6.9pt; }
    .info { height: 175px; white-space: pre-line; }
    .footer td { height: 30px; border: 1px solid #000; }
    .watermark { position: fixed; top: 38%; left: 0; width: 100%; text-align: center; font-size: 70pt; color: rgba(200, 0, 0, 0.18); transform: rotate(-30deg); font-weight: bold; }
</style>
</head>
<body>
@if ($d['watermark'])
    <div class="watermark">{{ $d['watermark'] }}</div>
@endif
<div class="frame">
    <table class="header">
        <tr>
            <td style="width: 30%"><img src="{{ $d['logo'] }}" style="height: 30px"></td>
            <td style="width: 40%" class="title">DANFSe v2.0<br>Documento Auxiliar da NFS-e</td>
            <td style="width: 30%"><span style="font-size: 9pt">Município: {{ $d['city'] }}</span><br><span class="muted">Ambiente Gerador: {{ $d['generator_environment'] }}<br>Tipo de Ambiente: {{ $d['environment'] }}</span></td>
        </tr>
    </table>

    <table class="section">
        <tr>
            <td colspan="3"><span class="label">CHAVE DE ACESSO DA NFS-e</span><span class="value key">{{ $d['access_key'] }}</span></td>
            <td rowspan="4" style="width: 25%; text-align: center"><img src="{{ $d['qr_code'] }}" style="width: 62px; height: 62px"><br><span class="muted">A autenticidade desta NFS-e pode ser verificada pela leitura deste código QR ou pela consulta da chave de acesso no portal nacional da NFS-e</span></td>
        </tr>
        <tr>
            <td><span class="label">NÚMERO DA NFS-e</span><span class="value">{{ $d['number'] }}</span></td>
            <td><span class="label">COMPETÊNCIA DA NFS-e</span><span class="value">{{ $d['competence'] }}</span></td>
            <td><span class="label">DATA E HORA DA EMISSÃO DA NFS-e</span><span class="value">{{ $d['processed_at'] }}</span></td>
        </tr>
        <tr>
            <td><span class="label">NÚMERO DA DPS</span><span class="value">{{ $d['dps_number'] }}</span></td>
            <td><span class="label">SÉRIE DA DPS</span><span class="value">{{ $d['dps_series'] }}</span></td>
            <td><span class="label">DATA E HORA DA EMISSÃO DA DPS</span><span class="value">{{ $d['dps_issued_at'] }}</span></td>
        </tr>
        <tr>
            <td class="shade"><span class="label">EMITENTE DA NFS-e</span><span class="value">{{ $d['issuer_role'] }}</span></td>
            <td><span class="label">SITUAÇÃO DA NFS-e</span><span class="value">{{ $d['status'] }}</span></td>
            <td><span class="label">FINALIDADE</span><span class="value">{{ $d['purpose'] }}</span></td>
        </tr>
    </table>

    @foreach ([['PRESTADOR / FORNECEDOR', $d['provider'], true], ['TOMADOR / ADQUIRENTE', $d['taker'], false]] as [$title, $p, $isProvider])
        @if ($p)
            <table class="section">
                <tr>
                    <td class="section-title">{{ $title }}</td>
                    <td><span class="label">CNPJ / CPF / NIF</span><span class="value">{{ $p['document'] }}</span></td>
                    <td><span class="label">Indicador Municipal (Inscrição)</span><span class="value">{{ $p['municipal_registration'] }}</span></td>
                    <td><span class="label">Telefone</span><span class="value">{{ $p['phone'] }}</span></td>
                </tr>
                <tr>
                    <td colspan="2"><span class="label">Nome / Nome Empresarial</span><span class="value">{{ $p['name'] }}</span></td>
                    <td><span class="label">Município / Sigla UF</span><span class="value">{{ $p['city'] }}</span></td>
                    <td><span class="label">Código IBGE / CEP</span><span class="value">{{ $p['ibge_zip'] }}</span></td>
                </tr>
                <tr>
                    <td colspan="2"><span class="label">Endereço</span><span class="value">{{ $p['address'] }}</span></td>
                    <td colspan="2"><span class="label">E-mail</span><span class="value">{{ $p['email'] }}</span></td>
                </tr>
                @if ($isProvider)
                    <tr>
                        <td><span class="label">Simples Nacional na Data de Competência</span><span class="value">{{ \Illuminate\Support\Str::limit($d['simples'], 34) }}</span></td>
                        <td colspan="3"><span class="label">Regime de Apuração Tributária pelo SN</span><span class="value">{{ $d['simples_regime'] }}</span></td>
                    </tr>
                @endif
            </table>
        @else
            <table class="section"><tr><td class="center">{{ $title === 'TOMADOR / ADQUIRENTE' ? 'TOMADOR/ADQUIRENTE NÃO IDENTIFICADO NA NFS-e' : $title }}</td></tr></table>
        @endif
    @endforeach

    @foreach ([['DESTINATÁRIO DA OPERAÇÃO', $d['recipient']], ['INTERMEDIÁRIO DA OPERAÇÃO', $d['intermediary']]] as [$title, $p])
        @if ($p)
            <table class="section">
                <tr>
                    <td class="section-title">{{ $title }}</td>
                    <td><span class="label">CNPJ / CPF / NIF</span><span class="value">{{ $p['document'] }}</span></td>
                    <td><span class="label">Indicador Municipal (Inscrição)</span><span class="value">{{ $p['municipal_registration'] }}</span></td>
                    <td><span class="label">Telefone</span><span class="value">{{ $p['phone'] }}</span></td>
                </tr>
                <tr>
                    <td colspan="2"><span class="label">Nome / Nome Empresarial</span><span class="value">{{ $p['name'] }}</span></td>
                    <td><span class="label">Município / Sigla UF</span><span class="value">{{ $p['city'] }}</span></td>
                    <td><span class="label">Código IBGE / CEP</span><span class="value">{{ $p['ibge_zip'] }}</span></td>
                </tr>
                <tr>
                    <td colspan="2"><span class="label">Endereço</span><span class="value">{{ $p['address'] }}</span></td>
                    <td colspan="2"><span class="label">E-mail</span><span class="value">{{ $p['email'] }}</span></td>
                </tr>
            </table>
        @else
            <table class="section"><tr><td class="center">{{ $title }} NÃO IDENTIFICADO NA NFS-e</td></tr></table>
        @endif
    @endforeach

    <table class="section">
        <tr>
            <td class="section-title">SERVIÇO PRESTADO</td>
            <td><span class="label">Código de Tributação Nacional/Municipal</span><span class="value">{{ $d['service_code'] }}</span></td>
            <td><span class="label">Código da NBS</span><span class="value">{{ $d['nbs'] }}</span></td>
            <td><span class="label">Local da Prestação / Sigla UF / País</span><span class="value">{{ $d['service_place'] }}</span></td>
        </tr>
        <tr><td colspan="4"><span class="value">{{ \Illuminate\Support\Str::limit($d['national_tax_description'], 180) }}</span></td></tr>
        <tr><td colspan="4"><span class="label">Descrição do Serviço</span><span class="value">{{ $d['description'] }}</span></td></tr>
    </table>

    <table class="section">
        <tr>
            <td class="section-title">TRIBUTAÇÃO MUNICIPAL (ISSQN)</td>
            <td><span class="label">Tipo de Tributação do ISSQN</span><span class="value">{{ $d['issqn_taxation'] }}</span></td>
            <td colspan="2"><span class="label">Município / Sigla UF / País de Incidência do ISSQN</span><span class="value">{{ $d['issqn_place'] }}</span></td>
        </tr>
        <tr>
            <td><span class="label">BC ISSQN</span><span class="value">{{ $d['issqn_base'] }}</span></td>
            <td><span class="label">Alíquota Aplicada</span><span class="value">{{ $d['issqn_rate'] }}</span></td>
            <td><span class="label">Retenção do ISSQN</span><span class="value">{{ $d['issqn_withholding'] }}</span></td>
            <td><span class="label">ISSQN Apurado</span><span class="value">{{ $d['issqn_amount'] }}</span></td>
        </tr>
    </table>

    <table class="section">
        <tr>
            <td class="section-title">TRIBUTAÇÃO FEDERAL (EXCETO CBS)</td>
            <td><span class="label">IRRF</span><span class="value">{{ $d['irrf'] }}</span></td>
            <td><span class="label">Contribuição Previdenciária - Retida</span><span class="value">{{ $d['cp'] }}</span></td>
            <td><span class="label">Contribuições Sociais - Retidas</span><span class="value">{{ $d['csll'] }}</span></td>
        </tr>
        <tr>
            <td><span class="label">PIS - Débito Apuração Própria</span><span class="value">{{ $d['pis'] }}</span></td>
            <td><span class="label">COFINS - Débito Apuração Própria</span><span class="value">{{ $d['cofins'] }}</span></td>
            <td colspan="2"><span class="label">Descrição Contrib. Sociais - Retidas</span><span class="value">{{ $d['federal_withholding'] }}</span></td>
        </tr>
    </table>

    <table class="section">
        <tr>
            <td class="section-title">TRIBUTAÇÃO IBS/CBS</td>
            <td><span class="label">CST / cClassTrib</span><span class="value">{{ $d['ibs']['cst'] }}</span></td>
            <td colspan="2"><span class="label">Indicador de Operação / Código IBGE Incidência / Município Incidência / Sigla UF</span><span class="value">{{ $d['ibs']['operation'] }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Exclusões e Reduções da Base de Cálculo</span><span class="value">{{ $d['ibs']['reductions'] }}</span></td>
            <td><span class="label">Base de Cálculo Após Exclusões e Reduções</span><span class="value">{{ $d['ibs']['base'] }}</span></td>
            <td><span class="label">Red. Alíquota IBS / Red. Alíquota CBS</span><span class="value">{{ $d['ibs']['rate_reduction'] }}</span></td>
            <td><span class="label">Alíquota - IBS UF / IBS Mun</span><span class="value">{{ $d['ibs']['rates'] }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Alíq. Efetiva Municipal - IBS</span><span class="value">{{ $d['ibs']['effective_mun'] }}</span></td>
            <td><span class="label">Valor Apurado Municipal - IBS</span><span class="value">{{ $d['ibs']['amount_mun'] }}</span></td>
            <td><span class="label">Alíq. Efetiva Estadual - IBS</span><span class="value">{{ $d['ibs']['effective_uf'] }}</span></td>
            <td><span class="label">Valor Apurado Estadual - IBS</span><span class="value">{{ $d['ibs']['amount_uf'] }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Valor Total Apurado - IBS</span><span class="value">{{ $d['ibs']['total_ibs'] }}</span></td>
            <td><span class="label">Alíquota - CBS</span><span class="value">{{ $d['ibs']['cbs_rate'] }}</span></td>
            <td><span class="label">Alíquota Efetiva - CBS</span><span class="value">{{ $d['ibs']['cbs_effective'] }}</span></td>
            <td><span class="label">Valor Total Apurado - CBS</span><span class="value">{{ $d['ibs']['total_cbs'] }}</span></td>
        </tr>
    </table>

    <table class="section">
        <tr>
            <td class="section-title">VALOR TOTAL DA NFS-e</td>
            <td><span class="label">VALOR DA OPERAÇÃO / SERVIÇO</span><span class="value">{{ $d['service_amount'] }}</span></td>
            <td><span class="label">Desconto Incondicionado</span><span class="value">{{ $d['unconditional_discount'] }}</span></td>
            <td><span class="label">Desconto Condicionado</span><span class="value">{{ $d['conditional_discount'] }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Total das Retenções (ISSQN / Federais)</span><span class="value">{{ $d['total_retained'] }}</span></td>
            <td><span class="label">VALOR LÍQUIDO DA NFS-e</span><span class="value">{{ $d['net_amount'] }}</span></td>
            <td><span class="label">Total do IBS/CBS</span><span class="value">{{ $d['total_ibs_cbs'] }}</span></td>
            <td class="shade"><span class="label">VALOR LÍQUIDO DA NFS-e + IBS/CBS</span><span class="value">{{ $d['net_plus_ibs_cbs'] }}</span></td>
        </tr>
    </table>

    <table class="section">
        <tr><td class="section-title" style="background: none">INFORMAÇÕES COMPLEMENTARES</td></tr>
        <tr><td class="info">{{ $d['additional_info'] }}</td></tr>
    </table>

    <table class="footer" style="margin: 0 6px 6px; width: calc(100% - 12px)">
        <tr>
            <td style="width: 25%"><span class="label">DATA CIENTIFICAÇÃO:</span></td>
            <td style="width: 25%"><span class="label">IDENTIFICAÇÃO E ASSINATURA</span></td>
            <td style="width: 50%"><span class="label">N° NFS-e / CHAVE NFS-e</span><span class="value">{{ $d['number'] }} / {{ $d['access_key'] }}</span></td>
        </tr>
    </table>
</div>
</body>
</html>
