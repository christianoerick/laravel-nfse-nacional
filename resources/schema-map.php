<?php

// Gerado por tools/generate.php a partir dos XSD oficiais. Não editar à mão.

return [
    'roots' => [
        'CNC' => 'TCNC',
        'DPS' => 'TCDPS',
        'NFSe' => 'TCNFSe',
        'evento' => 'TCEvento',
        'pedRegEvento' => 'TCPedRegEvt',
    ],
    'types' => [
        'TCAtvEvento' => [
            'attributes' => [],
            'children' => [
                ['xNome', NULL, false, NULL],
                ['dtIni', NULL, false, 'date'],
                ['dtFim', NULL, false, 'date'],
                ['idAtvEvt', NULL, false, NULL],
                ['end', 'TCEnderecoSimples', false, NULL],
            ],
        ],
        'TCBeneficioMunicipal' => [
            'attributes' => [],
            'children' => [
                ['nBM', NULL, false, NULL],
                ['vRedBCBM', NULL, false, 'decimal:2'],
                ['pRedBCBM', NULL, false, 'decimal:2'],
            ],
        ],
        'TCCServ' => [
            'attributes' => [],
            'children' => [
                ['cTribNac', NULL, false, NULL],
                ['cTribMun', NULL, false, NULL],
                ['xDescServ', NULL, false, NULL],
                ['cNBS', NULL, false, NULL],
                ['cIntContrib', NULL, false, NULL],
            ],
        ],
        'TCComExterior' => [
            'attributes' => [],
            'children' => [
                ['mdPrestacao', NULL, false, NULL],
                ['vincPrest', NULL, false, NULL],
                ['tpMoeda', NULL, false, NULL],
                ['vServMoeda', NULL, false, 'decimal:2'],
                ['mecAFComexP', NULL, false, NULL],
                ['mecAFComexT', NULL, false, NULL],
                ['movTempBens', NULL, false, NULL],
                ['nDI', NULL, false, NULL],
                ['nRE', NULL, false, NULL],
                ['mdic', NULL, false, NULL],
            ],
        ],
        'TCDPS' => [
            'attributes' => ['versao'],
            'children' => [
                ['infDPS', 'TCInfDPS', false, NULL],
                ['Signature', NULL, false, NULL],
            ],
        ],
        'TCDocDedRed' => [
            'attributes' => [],
            'children' => [
                ['chNFSe', NULL, false, NULL],
                ['chNFe', NULL, false, NULL],
                ['NFSeMun', 'TCDocOutNFSe', false, NULL],
                ['NFNFS', 'TCDocNFNFS', false, NULL],
                ['nDocFisc', NULL, false, NULL],
                ['nDoc', NULL, false, NULL],
                ['tpDedRed', NULL, false, NULL],
                ['xDescOutDed', NULL, false, NULL],
                ['dtEmiDoc', NULL, false, 'date'],
                ['vDedutivelRedutivel', NULL, false, 'decimal:2'],
                ['vDeducaoReducao', NULL, false, 'decimal:2'],
                ['fornec', 'TCInfoPessoa', false, NULL],
            ],
        ],
        'TCDocNFNFS' => [
            'attributes' => [],
            'children' => [
                ['nNFS', NULL, false, NULL],
                ['modNFS', NULL, false, NULL],
                ['serieNFS', NULL, false, NULL],
            ],
        ],
        'TCDocOutNFSe' => [
            'attributes' => [],
            'children' => [
                ['cMunNFSeMun', NULL, false, NULL],
                ['nNFSeMun', NULL, false, NULL],
                ['cVerifNFSeMun', NULL, false, NULL],
            ],
        ],
        'TCEmitente' => [
            'attributes' => [],
            'children' => [
                ['CNPJ', NULL, false, NULL],
                ['CPF', NULL, false, NULL],
                ['IM', NULL, false, NULL],
                ['xNome', NULL, false, NULL],
                ['xFant', NULL, false, NULL],
                ['enderNac', 'TCEnderecoEmitente', false, NULL],
                ['fone', NULL, false, NULL],
                ['email', NULL, false, NULL],
            ],
        ],
        'TCEnderContribuinteCNC' => [
            'attributes' => [],
            'children' => [
                ['CEP', NULL, false, NULL],
                ['nro', NULL, false, NULL],
                ['xCpl', NULL, false, NULL],
            ],
        ],
        'TCEnderExt' => [
            'attributes' => [],
            'children' => [
                ['cPais', NULL, false, NULL],
                ['cEndPost', NULL, false, NULL],
                ['xCidade', NULL, false, NULL],
                ['xEstProvReg', NULL, false, NULL],
            ],
        ],
        'TCEnderExtSimples' => [
            'attributes' => [],
            'children' => [
                ['cEndPost', NULL, false, NULL],
                ['xCidade', NULL, false, NULL],
                ['xEstProvReg', NULL, false, NULL],
            ],
        ],
        'TCEnderNac' => [
            'attributes' => [],
            'children' => [
                ['cMun', NULL, false, NULL],
                ['CEP', NULL, false, NULL],
            ],
        ],
        'TCEnderObraEvento' => [
            'attributes' => [],
            'children' => [
                ['CEP', NULL, false, NULL],
                ['endExt', 'TCEnderExtSimples', false, NULL],
                ['xLgr', NULL, false, NULL],
                ['nro', NULL, false, NULL],
                ['xCpl', NULL, false, NULL],
                ['xBairro', NULL, false, NULL],
            ],
        ],
        'TCEndereco' => [
            'attributes' => [],
            'children' => [
                ['endNac', 'TCEnderNac', false, NULL],
                ['endExt', 'TCEnderExt', false, NULL],
                ['xLgr', NULL, false, NULL],
                ['nro', NULL, false, NULL],
                ['xCpl', NULL, false, NULL],
                ['xBairro', NULL, false, NULL],
            ],
        ],
        'TCEnderecoEmitente' => [
            'attributes' => [],
            'children' => [
                ['xLgr', NULL, false, NULL],
                ['nro', NULL, false, NULL],
                ['xCpl', NULL, false, NULL],
                ['xBairro', NULL, false, NULL],
                ['cMun', NULL, false, NULL],
                ['UF', NULL, false, NULL],
                ['CEP', NULL, false, NULL],
            ],
        ],
        'TCEnderecoSimples' => [
            'attributes' => [],
            'children' => [
                ['CEP', NULL, false, NULL],
                ['endExt', 'TCEnderExtSimples', false, NULL],
                ['xLgr', NULL, false, NULL],
                ['nro', NULL, false, NULL],
                ['xCpl', NULL, false, NULL],
                ['xBairro', NULL, false, NULL],
            ],
        ],
        'TCEvento' => [
            'attributes' => ['versao'],
            'children' => [
                ['infEvento', 'TCInfEvento', false, NULL],
                ['Signature', NULL, false, NULL],
            ],
        ],
        'TCExigSuspensa' => [
            'attributes' => [],
            'children' => [
                ['tpSusp', NULL, false, NULL],
                ['nProcesso', NULL, false, NULL],
            ],
        ],
        'TCInfCNC' => [
            'attributes' => [],
            'children' => [
                ['tpAmb', NULL, false, NULL],
                ['dhGeracaoArquivo', NULL, false, 'datetime'],
                ['verAplic', NULL, false, NULL],
                ['contribuintesCnc', 'TCInfCNC_contribuintesCnc', false, NULL],
            ],
        ],
        'TCInfCNC_contribuintesCnc' => [
            'attributes' => [],
            'children' => [
                ['contribuinteCnc', 'TCInfoContribuinteCNC', true, NULL],
            ],
        ],
        'TCInfDPS' => [
            'attributes' => ['Id'],
            'children' => [
                ['tpAmb', NULL, false, NULL],
                ['dhEmi', NULL, false, 'datetime'],
                ['verAplic', NULL, false, NULL],
                ['serie', NULL, false, NULL],
                ['nDPS', NULL, false, NULL],
                ['dCompet', NULL, false, 'date'],
                ['tpEmit', NULL, false, NULL],
                ['cMotivoEmisTI', NULL, false, NULL],
                ['chNFSeRej', NULL, false, NULL],
                ['cLocEmi', NULL, false, NULL],
                ['subst', 'TCSubstituicao', false, NULL],
                ['prest', 'TCInfoPrestador', false, NULL],
                ['toma', 'TCInfoPessoa', false, NULL],
                ['interm', 'TCInfoPessoa', false, NULL],
                ['serv', 'TCServ', false, NULL],
                ['valores', 'TCInfoValores', false, NULL],
                ['IBSCBS', 'TCRTCInfoIBSCBS', false, NULL],
            ],
        ],
        'TCInfEvento' => [
            'attributes' => ['Id'],
            'children' => [
                ['verAplic', NULL, false, NULL],
                ['ambGer', NULL, false, NULL],
                ['nSeqEvento', NULL, false, NULL],
                ['dhProc', NULL, false, 'datetime'],
                ['nDFSe', NULL, false, NULL],
                ['pedRegEvento', 'TCPedRegEvt', false, NULL],
            ],
        ],
        'TCInfNFSe' => [
            'attributes' => ['Id'],
            'children' => [
                ['xLocEmi', NULL, false, NULL],
                ['xLocPrestacao', NULL, false, NULL],
                ['nNFSe', NULL, false, NULL],
                ['cLocIncid', NULL, false, NULL],
                ['xLocIncid', NULL, false, NULL],
                ['xTribNac', NULL, false, NULL],
                ['xTribMun', NULL, false, NULL],
                ['xNBS', NULL, false, NULL],
                ['verAplic', NULL, false, NULL],
                ['ambGer', NULL, false, NULL],
                ['tpEmis', NULL, false, NULL],
                ['procEmi', NULL, false, NULL],
                ['cStat', NULL, false, NULL],
                ['dhProc', NULL, false, 'datetime'],
                ['nDFSe', NULL, false, NULL],
                ['emit', 'TCEmitente', false, NULL],
                ['valores', 'TCValoresNFSe', false, NULL],
                ['xOutInf', NULL, false, NULL],
                ['IBSCBS', 'TCRTCIBSCBS', false, NULL],
                ['DPS', 'TCDPS', false, NULL],
            ],
        ],
        'TCInfPedReg' => [
            'attributes' => ['Id'],
            'children' => [
                ['tpAmb', NULL, false, NULL],
                ['verAplic', NULL, false, NULL],
                ['dhEvento', NULL, false, 'datetime'],
                ['CNPJAutor', NULL, false, NULL],
                ['CPFAutor', NULL, false, NULL],
                ['chNFSe', NULL, false, NULL],
                ['e101101', 'TE101101', false, NULL],
                ['e105102', 'TE105102', false, NULL],
                ['e101103', 'TE101103', false, NULL],
                ['e105104', 'TE105104', false, NULL],
                ['e105105', 'TE105105', false, NULL],
                ['e202201', 'TE202201', false, NULL],
                ['e203202', 'TE203202', false, NULL],
                ['e204203', 'TE204203', false, NULL],
                ['e205204', 'TE205204', false, NULL],
                ['e202205', 'TE202205', false, NULL],
                ['e203206', 'TE203206', false, NULL],
                ['e204207', 'TE204207', false, NULL],
                ['e205208', 'TE205208', false, NULL],
                ['e305101', 'TE305101', false, NULL],
                ['e305102', 'TE305102', false, NULL],
                ['e305103', 'TE305103', false, NULL],
            ],
        ],
        'TCInfoCompl' => [
            'attributes' => [],
            'children' => [
                ['idDocTec', NULL, false, NULL],
                ['docRef', NULL, false, NULL],
                ['xPed', NULL, false, NULL],
                ['gItemPed', 'TCInfoItemPed', false, NULL],
                ['xInfComp', NULL, false, NULL],
            ],
        ],
        'TCInfoContribuinteCNC' => [
            'attributes' => [],
            'children' => [
                ['CNPJ', NULL, false, NULL],
                ['CPF', NULL, false, NULL],
                ['IM', NULL, false, NULL],
                ['dInscricaoMunicipal', NULL, false, 'date'],
                ['enderContribuinteCNC', 'TCEnderContribuinteCNC', false, NULL],
                ['fone', NULL, false, NULL],
                ['email', NULL, false, NULL],
                ['regEspTribContribuinteCNC', NULL, false, NULL],
                ['situacaoCadastroContribuinte', NULL, false, NULL],
                ['motivoSituacaoCadastroContribuinte', NULL, false, NULL],
                ['situacaoEmissaoNFSE', NULL, false, NULL],
            ],
        ],
        'TCInfoDedRed' => [
            'attributes' => [],
            'children' => [
                ['pDR', NULL, false, 'decimal:2'],
                ['vDR', NULL, false, 'decimal:2'],
                ['documentos', 'TCListaDocDedRed', false, NULL],
            ],
        ],
        'TCInfoItemPed' => [
            'attributes' => [],
            'children' => [
                ['xItemPed', NULL, true, NULL],
            ],
        ],
        'TCInfoObra' => [
            'attributes' => [],
            'children' => [
                ['inscImobFisc', NULL, false, NULL],
                ['cObra', NULL, false, NULL],
                ['cCIB', NULL, false, NULL],
                ['end', 'TCEnderObraEvento', false, NULL],
            ],
        ],
        'TCInfoPessoa' => [
            'attributes' => [],
            'children' => [
                ['CNPJ', NULL, false, NULL],
                ['CPF', NULL, false, NULL],
                ['NIF', NULL, false, NULL],
                ['cNaoNIF', NULL, false, NULL],
                ['CAEPF', NULL, false, NULL],
                ['IM', NULL, false, NULL],
                ['xNome', NULL, false, NULL],
                ['end', 'TCEndereco', false, NULL],
                ['fone', NULL, false, NULL],
                ['email', NULL, false, NULL],
            ],
        ],
        'TCInfoPrestador' => [
            'attributes' => [],
            'children' => [
                ['CNPJ', NULL, false, NULL],
                ['CPF', NULL, false, NULL],
                ['NIF', NULL, false, NULL],
                ['cNaoNIF', NULL, false, NULL],
                ['CAEPF', NULL, false, NULL],
                ['IM', NULL, false, NULL],
                ['xNome', NULL, false, NULL],
                ['end', 'TCEndereco', false, NULL],
                ['fone', NULL, false, NULL],
                ['email', NULL, false, NULL],
                ['regTrib', 'TCRegTrib', false, NULL],
            ],
        ],
        'TCInfoRefNFSe' => [
            'attributes' => [],
            'children' => [
                ['refNFSe', NULL, true, NULL],
            ],
        ],
        'TCInfoTributacao' => [
            'attributes' => [],
            'children' => [
                ['tribMun', 'TCTribMunicipal', false, NULL],
                ['tribFed', 'TCTribFederal', false, NULL],
                ['totTrib', 'TCTribTotal', false, NULL],
            ],
        ],
        'TCInfoValores' => [
            'attributes' => [],
            'children' => [
                ['vServPrest', 'TCVServPrest', false, NULL],
                ['vDescCondIncond', 'TCVDescCondIncond', false, NULL],
                ['vDedRed', 'TCInfoDedRed', false, NULL],
                ['trib', 'TCInfoTributacao', false, NULL],
            ],
        ],
        'TCListaDocDedRed' => [
            'attributes' => [],
            'children' => [
                ['docDedRed', 'TCDocDedRed', true, NULL],
            ],
        ],
        'TCLocPrest' => [
            'attributes' => [],
            'children' => [
                ['cLocPrestacao', NULL, false, NULL],
                ['cPaisPrestacao', NULL, false, NULL],
            ],
        ],
        'TCNC' => [
            'attributes' => ['versao'],
            'children' => [
                ['infCNC', 'TCInfCNC', false, NULL],
            ],
        ],
        'TCNFSe' => [
            'attributes' => ['versao'],
            'children' => [
                ['infNFSe', 'TCInfNFSe', false, NULL],
                ['Signature', NULL, false, NULL],
            ],
        ],
        'TCPedRegEvt' => [
            'attributes' => ['versao'],
            'children' => [
                ['infPedReg', 'TCInfPedReg', false, NULL],
                ['Signature', NULL, false, NULL],
            ],
        ],
        'TCRTCIBSCBS' => [
            'attributes' => [],
            'children' => [
                ['cLocalidadeIncid', NULL, false, NULL],
                ['xLocalidadeIncid', NULL, false, NULL],
                ['pRedutor', NULL, false, 'decimal:2'],
                ['valores', 'TCRTCValoresIBSCBS', false, NULL],
                ['totCIBS', 'TCRTCTotalCIBS', false, NULL],
            ],
        ],
        'TCRTCInfoDest' => [
            'attributes' => [],
            'children' => [
                ['CNPJ', NULL, false, NULL],
                ['CPF', NULL, false, NULL],
                ['NIF', NULL, false, NULL],
                ['cNaoNIF', NULL, false, NULL],
                ['xNome', NULL, false, NULL],
                ['end', 'TCEndereco', false, NULL],
                ['fone', NULL, false, NULL],
                ['email', NULL, false, NULL],
            ],
        ],
        'TCRTCInfoIBSCBS' => [
            'attributes' => [],
            'children' => [
                ['finNFSe', NULL, false, NULL],
                ['indFinal', NULL, false, NULL],
                ['cIndOp', NULL, false, NULL],
                ['tpOper', NULL, false, NULL],
                ['gRefNFSe', 'TCInfoRefNFSe', false, NULL],
                ['tpEnteGov', NULL, false, NULL],
                ['indDest', NULL, false, NULL],
                ['dest', 'TCRTCInfoDest', false, NULL],
                ['imovel', 'TCRTCInfoImovel', false, NULL],
                ['valores', 'TCRTCInfoValoresIBSCBS', false, NULL],
            ],
        ],
        'TCRTCInfoImovel' => [
            'attributes' => [],
            'children' => [
                ['inscImobFisc', NULL, false, NULL],
                ['cCIB', NULL, false, NULL],
                ['end', 'TCEnderObraEvento', false, NULL],
            ],
        ],
        'TCRTCInfoReeRepRes' => [
            'attributes' => [],
            'children' => [
                ['documentos', 'TCRTCListaDoc', true, NULL],
            ],
        ],
        'TCRTCInfoTributosDif' => [
            'attributes' => [],
            'children' => [
                ['pDifUF', NULL, false, 'decimal:2'],
                ['pDifMun', NULL, false, 'decimal:2'],
                ['pDifCBS', NULL, false, 'decimal:2'],
            ],
        ],
        'TCRTCInfoTributosIBSCBS' => [
            'attributes' => [],
            'children' => [
                ['gIBSCBS', 'TCRTCInfoTributosSitClas', false, NULL],
            ],
        ],
        'TCRTCInfoTributosSitClas' => [
            'attributes' => [],
            'children' => [
                ['CST', NULL, false, NULL],
                ['cClassTrib', NULL, false, NULL],
                ['cCredPres', NULL, false, NULL],
                ['gTribRegular', 'TCRTCInfoTributosTribRegular', false, NULL],
                ['gDif', 'TCRTCInfoTributosDif', false, NULL],
            ],
        ],
        'TCRTCInfoTributosTribRegular' => [
            'attributes' => [],
            'children' => [
                ['CSTReg', NULL, false, NULL],
                ['cClassTribReg', NULL, false, NULL],
            ],
        ],
        'TCRTCInfoValoresIBSCBS' => [
            'attributes' => [],
            'children' => [
                ['gReeRepRes', 'TCRTCInfoReeRepRes', false, NULL],
                ['trib', 'TCRTCInfoTributosIBSCBS', false, NULL],
            ],
        ],
        'TCRTCListaDoc' => [
            'attributes' => [],
            'children' => [
                ['dFeNacional', 'TCRTCListaDocDFe', false, NULL],
                ['docFiscalOutro', 'TCRTCListaDocFiscalOutro', false, NULL],
                ['docOutro', 'TCRTCListaDocOutro', false, NULL],
                ['fornec', 'TCRTCListaDocFornec', false, NULL],
                ['dtEmiDoc', NULL, false, 'date'],
                ['dtCompDoc', NULL, false, 'date'],
                ['tpReeRepRes', NULL, false, NULL],
                ['xTpReeRepRes', NULL, false, NULL],
                ['vlrReeRepRes', NULL, false, 'decimal:2'],
            ],
        ],
        'TCRTCListaDocDFe' => [
            'attributes' => [],
            'children' => [
                ['tipoChaveDFe', NULL, false, NULL],
                ['xTipoChaveDFe', NULL, false, NULL],
                ['chaveDFe', NULL, false, NULL],
            ],
        ],
        'TCRTCListaDocFiscalOutro' => [
            'attributes' => [],
            'children' => [
                ['cMunDocFiscal', NULL, false, NULL],
                ['nDocFiscal', NULL, false, NULL],
                ['xDocFiscal', NULL, false, NULL],
            ],
        ],
        'TCRTCListaDocFornec' => [
            'attributes' => [],
            'children' => [
                ['CNPJ', NULL, false, NULL],
                ['CPF', NULL, false, NULL],
                ['NIF', NULL, false, NULL],
                ['cNaoNIF', NULL, false, NULL],
                ['xNome', NULL, false, NULL],
            ],
        ],
        'TCRTCListaDocOutro' => [
            'attributes' => [],
            'children' => [
                ['nDoc', NULL, false, NULL],
                ['xDoc', NULL, false, NULL],
            ],
        ],
        'TCRTCTotalCBS' => [
            'attributes' => [],
            'children' => [
                ['gCBSCredPres', 'TCRTCTotalCBSCredPres', false, NULL],
                ['vDifCBS', NULL, false, 'decimal:2'],
                ['vCBS', NULL, false, 'decimal:2'],
            ],
        ],
        'TCRTCTotalCBSCredPres' => [
            'attributes' => [],
            'children' => [
                ['pCredPresCBS', NULL, false, 'decimal:2'],
                ['vCredPresCBS', NULL, false, 'decimal:2'],
            ],
        ],
        'TCRTCTotalCIBS' => [
            'attributes' => [],
            'children' => [
                ['vTotNF', NULL, false, 'decimal:2'],
                ['gIBS', 'TCRTCTotalIBS', false, NULL],
                ['gCBS', 'TCRTCTotalCBS', false, NULL],
                ['gTribRegular', 'TCRTCTotalTribRegular', false, NULL],
                ['gTribCompraGov', 'TCRTCTotalTribCompraGov', false, NULL],
            ],
        ],
        'TCRTCTotalIBS' => [
            'attributes' => [],
            'children' => [
                ['vIBSTot', NULL, false, 'decimal:2'],
                ['gIBSCredPres', 'TCRTCTotalIBSCredPres', false, NULL],
                ['gIBSUFTot', 'TCRTCTotalIBSUF', false, NULL],
                ['gIBSMunTot', 'TCRTCTotalIBSMun', false, NULL],
            ],
        ],
        'TCRTCTotalIBSCredPres' => [
            'attributes' => [],
            'children' => [
                ['pCredPresIBS', NULL, false, 'decimal:2'],
                ['vCredPresIBS', NULL, false, 'decimal:2'],
            ],
        ],
        'TCRTCTotalIBSMun' => [
            'attributes' => [],
            'children' => [
                ['vDifMun', NULL, false, 'decimal:2'],
                ['vIBSMun', NULL, false, 'decimal:2'],
            ],
        ],
        'TCRTCTotalIBSUF' => [
            'attributes' => [],
            'children' => [
                ['vDifUF', NULL, false, 'decimal:2'],
                ['vIBSUF', NULL, false, 'decimal:2'],
            ],
        ],
        'TCRTCTotalTribCompraGov' => [
            'attributes' => [],
            'children' => [
                ['pIBSUF', NULL, false, 'decimal:2'],
                ['vIBSUF', NULL, false, 'decimal:2'],
                ['pIBSMun', NULL, false, 'decimal:2'],
                ['vIBSMun', NULL, false, 'decimal:2'],
                ['pCBS', NULL, false, 'decimal:2'],
                ['vCBS', NULL, false, 'decimal:2'],
            ],
        ],
        'TCRTCTotalTribRegular' => [
            'attributes' => [],
            'children' => [
                ['pAliqEfeRegIBSUF', NULL, false, 'decimal:2'],
                ['vTribRegIBSUF', NULL, false, 'decimal:2'],
                ['pAliqEfeRegIBSMun', NULL, false, 'decimal:2'],
                ['vTribRegIBSMun', NULL, false, 'decimal:2'],
                ['pAliqEfeRegCBS', NULL, false, 'decimal:2'],
                ['vTribRegCBS', NULL, false, 'decimal:2'],
            ],
        ],
        'TCRTCValoresIBSCBS' => [
            'attributes' => [],
            'children' => [
                ['vBC', NULL, false, 'decimal:2'],
                ['vCalcReeRepRes', NULL, false, 'decimal:2'],
                ['uf', 'TCRTCValoresIBSCBSUF', false, NULL],
                ['mun', 'TCRTCValoresIBSCBSMun', false, NULL],
                ['fed', 'TCRTCValoresIBSCBSFed', false, NULL],
            ],
        ],
        'TCRTCValoresIBSCBSFed' => [
            'attributes' => [],
            'children' => [
                ['pCBS', NULL, false, 'decimal:2'],
                ['pRedAliqCBS', NULL, false, 'decimal:2'],
                ['pAliqEfetCBS', NULL, false, 'decimal:2'],
            ],
        ],
        'TCRTCValoresIBSCBSMun' => [
            'attributes' => [],
            'children' => [
                ['pIBSMun', NULL, false, 'decimal:2'],
                ['pRedAliqMun', NULL, false, 'decimal:2'],
                ['pAliqEfetMun', NULL, false, 'decimal:2'],
            ],
        ],
        'TCRTCValoresIBSCBSUF' => [
            'attributes' => [],
            'children' => [
                ['pIBSUF', NULL, false, 'decimal:2'],
                ['pRedAliqUF', NULL, false, 'decimal:2'],
                ['pAliqEfetUF', NULL, false, 'decimal:2'],
            ],
        ],
        'TCRegTrib' => [
            'attributes' => [],
            'children' => [
                ['opSimpNac', NULL, false, NULL],
                ['regApTribSN', NULL, false, NULL],
                ['regEspTrib', NULL, false, NULL],
            ],
        ],
        'TCServ' => [
            'attributes' => [],
            'children' => [
                ['locPrest', 'TCLocPrest', false, NULL],
                ['cServ', 'TCCServ', false, NULL],
                ['comExt', 'TCComExterior', false, NULL],
                ['obra', 'TCInfoObra', false, NULL],
                ['atvEvento', 'TCAtvEvento', false, NULL],
                ['infoCompl', 'TCInfoCompl', false, NULL],
            ],
        ],
        'TCSubstituicao' => [
            'attributes' => [],
            'children' => [
                ['chSubstda', NULL, false, NULL],
                ['cMotivo', NULL, false, NULL],
                ['xMotivo', NULL, false, NULL],
            ],
        ],
        'TCTribFederal' => [
            'attributes' => [],
            'children' => [
                ['piscofins', 'TCTribOutrosPisCofins', false, NULL],
                ['vRetCP', NULL, false, 'decimal:2'],
                ['vRetIRRF', NULL, false, 'decimal:2'],
                ['vRetCSLL', NULL, false, 'decimal:2'],
            ],
        ],
        'TCTribMunicipal' => [
            'attributes' => [],
            'children' => [
                ['tribISSQN', NULL, false, NULL],
                ['cPaisResult', NULL, false, NULL],
                ['tpImunidade', NULL, false, NULL],
                ['exigSusp', 'TCExigSuspensa', false, NULL],
                ['BM', 'TCBeneficioMunicipal', false, NULL],
                ['tpRetISSQN', NULL, false, NULL],
                ['pAliq', NULL, false, 'decimal:2'],
            ],
        ],
        'TCTribOutrosPisCofins' => [
            'attributes' => [],
            'children' => [
                ['CST', NULL, false, NULL],
                ['vBCPisCofins', NULL, false, 'decimal:2'],
                ['pAliqPis', NULL, false, 'decimal:2'],
                ['pAliqCofins', NULL, false, 'decimal:2'],
                ['vPis', NULL, false, 'decimal:2'],
                ['vCofins', NULL, false, 'decimal:2'],
                ['tpRetPisCofins', NULL, false, NULL],
            ],
        ],
        'TCTribTotal' => [
            'attributes' => [],
            'children' => [
                ['vTotTrib', 'TCTribTotalMonet', false, NULL],
                ['pTotTrib', 'TCTribTotalPercent', false, NULL],
                ['indTotTrib', NULL, false, NULL],
                ['pTotTribSN', NULL, false, 'decimal:2'],
            ],
        ],
        'TCTribTotalMonet' => [
            'attributes' => [],
            'children' => [
                ['vTotTribFed', NULL, false, 'decimal:2'],
                ['vTotTribEst', NULL, false, 'decimal:2'],
                ['vTotTribMun', NULL, false, 'decimal:2'],
            ],
        ],
        'TCTribTotalPercent' => [
            'attributes' => [],
            'children' => [
                ['pTotTribFed', NULL, false, 'decimal:2'],
                ['pTotTribEst', NULL, false, 'decimal:2'],
                ['pTotTribMun', NULL, false, 'decimal:2'],
            ],
        ],
        'TCVDescCondIncond' => [
            'attributes' => [],
            'children' => [
                ['vDescIncond', NULL, false, 'decimal:2'],
                ['vDescCond', NULL, false, 'decimal:2'],
            ],
        ],
        'TCVServPrest' => [
            'attributes' => [],
            'children' => [
                ['vReceb', NULL, false, 'decimal:2'],
                ['vServ', NULL, false, 'decimal:2'],
            ],
        ],
        'TCValoresNFSe' => [
            'attributes' => [],
            'children' => [
                ['vCalcDR', NULL, false, 'decimal:2'],
                ['tpBM', NULL, false, NULL],
                ['vCalcBM', NULL, false, 'decimal:2'],
                ['vBC', NULL, false, 'decimal:2'],
                ['pAliqAplic', NULL, false, 'decimal:2'],
                ['vISSQN', NULL, false, 'decimal:2'],
                ['vTotalRet', NULL, false, 'decimal:2'],
                ['vLiq', NULL, false, 'decimal:2'],
            ],
        ],
        'TE101101' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
                ['cMotivo', NULL, false, NULL],
                ['xMotivo', NULL, false, NULL],
            ],
        ],
        'TE101103' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
                ['cMotivo', NULL, false, NULL],
                ['xMotivo', NULL, false, NULL],
            ],
        ],
        'TE105102' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
                ['cMotivo', NULL, false, NULL],
                ['xMotivo', NULL, false, NULL],
                ['chSubstituta', NULL, false, NULL],
            ],
        ],
        'TE105104' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
                ['CPFAgTrib', NULL, false, NULL],
                ['nProcAdm', NULL, false, NULL],
                ['cMotivo', NULL, false, NULL],
                ['xMotivo', NULL, false, NULL],
            ],
        ],
        'TE105105' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
                ['CPFAgTrib', NULL, false, NULL],
                ['nProcAdm', NULL, false, NULL],
                ['cMotivo', NULL, false, NULL],
                ['xMotivo', NULL, false, NULL],
            ],
        ],
        'TE202201' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
            ],
        ],
        'TE202205' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
                ['cMotivo', NULL, false, NULL],
                ['xMotivo', NULL, false, NULL],
            ],
        ],
        'TE203202' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
            ],
        ],
        'TE203206' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
                ['cMotivo', NULL, false, NULL],
                ['xMotivo', NULL, false, NULL],
            ],
        ],
        'TE204203' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
            ],
        ],
        'TE204207' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
                ['cMotivo', NULL, false, NULL],
                ['xMotivo', NULL, false, NULL],
            ],
        ],
        'TE205204' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
            ],
        ],
        'TE205208' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
                ['CPFAgTrib', NULL, false, NULL],
                ['idEvManifRej', NULL, false, NULL],
                ['xMotivo', NULL, false, NULL],
            ],
        ],
        'TE305101' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
                ['CPFAgTrib', NULL, false, NULL],
                ['nProcAdm', NULL, false, NULL],
                ['xProcAdm', NULL, false, NULL],
            ],
        ],
        'TE305102' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
                ['CPFAgTrib', NULL, false, NULL],
                ['codEvento', NULL, false, NULL],
                ['xMotivo', NULL, false, NULL],
            ],
        ],
        'TE305103' => [
            'attributes' => [],
            'children' => [
                ['xDesc', NULL, false, NULL],
                ['CPFAgTrib', NULL, false, NULL],
                ['idBloqOfic', NULL, false, NULL],
            ],
        ],
    ],
];
