# Changelog

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
