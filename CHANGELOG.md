# Changelog

## 0.3.0 - 2026-11-11

- `NfseClient::check($dps)`: valida sem emitir. Confere certificado, XSD e assinatura no local e faz consultas só de leitura ao Sistema Nacional: convênio do município, código de serviço e alíquota, e se a DPS já foi usada.
- `SimulatedNfseClient` (`client(..., simulate: true)` ou `NFSE_NACIONAL_SIMULATE`): fluxo completo sem rede, com NFS-e simulada válida no XSD, assinada e marcada.
- Consultas de alíquota e regimes: o código vai no formato aceito pelo ADN (`01.05.01.001`). O código municipal entra por `municipalCode`, e sem ele é usado `000`.

## 0.2.0 - 2026-11-11

- **NT SE/CGNFS-e 009 v1.01**: XSD oficiais de 20/10/2026 ao lado dos de 09/02/2026 (`resources/schemas/{v1.01,nt009}`). Escolha automática por data e ambiente (produção restrita desde 20/10/2026, produção a partir de 01/12/2026), configurável (`layout`, `layout_switch`).
- O mesmo array de DPS funciona nos dois leiautes: `finNFSe` e `CST`/`cClassTrib` do IBS/CBS vão para o lugar certo; `vDedRed`/`vAjusteBC`, `gReeRepRes` e `bensMoveis` geram erro com a orientação.
- `NfseClient::layout()`, `Dps::toXml($layout)`, `XmlBuilder`/`SchemaValidator` por leiaute. Enum `BaseAdjustmentType` (ajuste de base da NT 009).
- Consulta de evento (`event()`): lê a resposta real do Sefin (lista `eventos`, `arquivoXml` em base64 duplo). `NfseClient::unpack()` para qualquer XML das respostas.
- DANFSe lê IBS/CBS nos dois leiautes.
- A configuração é lida a cada cliente (mudanças em runtime valem).

## 0.1.5 - 2026-11-11

- Eventos do contribuinte: análise fiscal para cancelamento (e101103), confirmação/rejeição do prestador (e202201/e202205) e do tomador (e203202/e203206); `EventRequest::fromCode()` e o catálogo `AuthorEvents` com os motivos.
- Enums `AnalysisCancellationReason` e `RejectionReason`.

## 0.1.4 - 2026-11-11

- DANFSe igual ao oficial: série sem zeros à esquerda, descrição municipal do serviço (xTribMun), "Simples Nacional" por extenso, "Operação Tributável" e totais aproximados com traços no Simples. Conferido com o PDF do portal.

## 0.1.3 - 2026-11-11

- DPS e pedido de evento vão com a declaração `<?xml version="1.0" encoding="UTF-8"?>`: sem ela o Sefin recusa com E1229. Validado em produção (NFS-e emitida em Maceió).

## 0.1.2 - 2026-11-11

- Logo do DANFSe com fundo branco (antes saía preto, com o texto ilegível).

## 0.1.1 - 2026-11-11

- Aceita chillerlan/php-qrcode 5 e 6.

## 0.1.0 - 2026-11-11

Primeira versão.

- DPS completa no leiaute 1.01 (ordem e validação pelo XSD oficial), assinatura XMLDSig SHA-1/SHA-256.
- Sefin Nacional: emissão (com recuperação de resposta perdida), consulta de NFS-e e DPS, eventos (cancelamento, substituição e genéricos).
- ADN: DANFSe oficial, parâmetros municipais, distribuição por NSU e eventos.
- DANFSe v2.0 gerado localmente.
- 39 enums do leiaute, lista nacional de serviços e códigos de rejeição.
- Laravel 12 e 13, PHP 8.2 a 8.5.
