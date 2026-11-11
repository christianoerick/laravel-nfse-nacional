# Mapa completo de campos

Gerado por `tools/generate.php` a partir dos XSD oficiais (leiaute 1.01). Use os nomes das tags como chaves do array; a ordem não importa, o `XmlBuilder` segue a do XSD.

Legenda da ocorrência: `1-1` obrigatório, `0-1` opcional, `0-n`/`1-n` lista. Grupos marcados com **(escolha)** aceitam só um dos filhos.

## DPS (emissão)

| Caminho | Ocor. | Tipo | Opções / formato | Descrição |
|---|---|---|---|---|
| `DPS/@versao` | 1-1 | TVerNFSe | maxLength=4, pattern=1\.00\|1\.01 |  |
| **`DPS/infDPS`** | 1-1 | grupo | |  |
| `DPS/infDPS/@Id` | 1-1 | TSIdDPS | maxLength=45, pattern=DPS[0-9]{42} |  |
| `DPS/infDPS/tpAmb` | 1-1 | TSTipoAmbiente | `1` Produção<br>`2` Homologação<br>→ `Enums\Environment` | Identificação do Ambiente: 1 - Produção; 2 - Homologação |
| `DPS/infDPS/dhEmi` | 1-1 | TSDateTimeUTC | pattern=(((20(([02468][048])\|([13579][26]))-02-29))\|(20[0-9][0-9])-((((0[1-9])\|(1[0-2]))-((0[1-9])\|(1\d)\|(2[0-8])))\|((((0[13578])\|(1[02]))-31)\|(((0[1,3-9])\|(1[0-2]))-(29\|30)))))T(20\|21\|22\|23\|[0-1]\d):[0-5]\d:[0-5]\d([\-,\+](0[0-9]\|10\|11):00\|([\+](12):00)) | Data e hora da emissão do DPS. Data e hora no formato UTC (Universal Coordinated Time): AAAA-MM-DDThh:mm:ssTZD |
| `DPS/infDPS/verAplic` | 1-1 | TSVerAplic | minLength=1, maxLength=20 | Versão do aplicativo que gerou o DPS |
| `DPS/infDPS/serie` | 1-1 | TSSerieDPS | maxLength=5, pattern=0{0,4}\d{1,5} | Número do equipamento emissor do DPS ou série do DPS |
| `DPS/infDPS/nDPS` | 1-1 | TSNumDPS | maxLength=15, pattern=[1-9]{1}[0-9]{0,14} | Número do DPS |
| `DPS/infDPS/dCompet` | 1-1 | TSData | pattern=(((20(([02468][048])\|([13579][26]))-02-29))\|(20[0-9][0-9])-((((0[1-9])\|(1[0-2]))-((0[1-9])\|(1\d)\|(2[0-8])))\|((((0[13578])\|(1[02]))-31)\|(((0[1,3-9])\|(1[0-2]))-(29\|30))))) | Data em que se iniciou a prestação do serviço: Dia, mês e ano (AAAAMMDD) |
| `DPS/infDPS/tpEmit` | 1-1 | TSEmitenteDPS | `1` Prestador<br>`2` Tomador<br>`3` Intermediário<br>→ `Enums\IssuerRole` | Emitente da DPS: 1 - Prestador; 2 - Tomador; 3 - Intermediário |
| `DPS/infDPS/cMotivoEmisTI` | 0-1 | TSMotivoEmisTI | `1` Importação de Serviço<br>`2` Tomador/Intermediário obrigado a emitir NFS-e por legislação municipal<br>`3` Tomador/Intermediário emitindo NFS-e por recusa de emissão pelo prestador<br>`4` Tomador/Intermediário emitindo por rejeitar a NFS-e emitida pelo prestador<br>→ `Enums\TakerIssueReason` | Motivo da Emissão da DPS pelo Tomador/Intermediário: 1 - Importação de Serviço; 2 - Tomador/Intermediário obrigado a emitir NFS-e por legislação municipal; 3 - Tomador/Intermediário emitindo NFS-e por recusa de emissão pelo prestador; 4 - Tomador/Intermediário emitindo por rejeitar a NFS-e emitida pelo prestador; |
| `DPS/infDPS/chNFSeRej` | 0-1 | TSChaveNFSe | maxLength=50, pattern=[0-9]{50} | Chave de Acesso da NFS-e rejeitada pelo Tomador/Intermediário. |
| `DPS/infDPS/cLocEmi` | 1-1 | TSCodMunIBGE | pattern=[0-9]{7} | O código de município utilizado pelo Sistema Nacional NFS-e é o código definido para cada município pertencente ao ""Anexo V – Tabela de Código de Municípios do IBGE"", que consta ao final do Manual de Orientação ao Contribuinte do ISSQN para a Sefin Nacional NFS-e. O município emissor da NFS-e é aquele município em que o emitente da DPS está cadastrado e autorizado a "emitir uma NFS-e", ou seja, emitir uma DPS para que o sistema nacional valide as informações nela prestadas e gere a NFS-e correspondente para o emitente. Para que o sistema nacional emita a NFS-e o município emissor deve ser c… |
| **`DPS/infDPS/subst`** | 0-1 | grupo | | Dados da NFS-e a ser substituída |
| `DPS/infDPS/subst/chSubstda` | 1-1 | TSChaveNFSe | maxLength=50, pattern=[0-9]{50} | Chave de acesso da NFS-e a ser substituída |
| `DPS/infDPS/subst/cMotivo` | 1-1 | TSCodJustSubst | `01` Desenquadramento de NFS-e do Simples Nacional<br>`02` Enquadramento de NFS-e no Simples Nacional<br>`03` Inclusão Retroativa de Imunidade/Isenção para NFS-e<br>`04` Exclusão Retroativa de Imunidade/Isenção para NFS-e<br>`05` Rejeição de NFS-e pelo tomador ou pelo intermediário se responsável pelo recolhimento do tributo<br>`99` Outros<br>→ `Enums\ReplacementReason` | Código de justificativa para substituição de NFS-e: 01 - Desenquadramento de NFS-e do Simples Nacional; 02 - Enquadramento de NFS-e no Simples Nacional; 03 - Inclusão Retroativa de Imunidade/Isenção para NFS-e; 04 - Exclusão Retroativa de Imunidade/Isenção para NFS-e; 05 - Rejeição de NFS-e pelo tomador ou pelo intermediário se responsável pelo recolhimento do tributo; 99 - Outros; |
| `DPS/infDPS/subst/xMotivo` | 0-1 | TSMotivo | minLength=15, maxLength=255 | Descrição do motivo da substituição da NFS-e |
| **`DPS/infDPS/prest`** | 1-1 | grupo | | Grupo de informações do DPS relativas ao Prestador de Serviços |
| `DPS/infDPS/prest/CNPJ` | 0-1 | TSCNPJ | maxLength=14, pattern=[0-9]{14} | Número do CNPJ **(escolha)** |
| `DPS/infDPS/prest/CPF` | 0-1 | TSCPF | maxLength=11, pattern=[0-9]{11} | Número do CPF **(escolha)** |
| `DPS/infDPS/prest/NIF` | 0-1 | TSNIF | minLength=1, maxLength=40 | Número de Identificação Fiscal fornecido por órgão de administração tributária no exterior **(escolha)** |
| `DPS/infDPS/prest/cNaoNIF` | 0-1 | TSCodNaoNIF | `0` Não informado na nota de origem<br>`1` Dispensado do NIF<br>`2` Não exigência do NIF<br>→ `Enums\NoNifReason` | Motivo para não informação do NIF: 0 - Não informado na nota de origem; 1 - Dispensado do NIF; 2 - Não exigência do NIF; **(escolha)** |
| `DPS/infDPS/prest/CAEPF` | 0-1 | TSCAEPF | maxLength=14, pattern=[0-9]{14} | Número do Cadastro de Atividade Econômica da Pessoa Física (CAEPF) do prestador do serviço. |
| `DPS/infDPS/prest/IM` | 0-1 | TSInscMun | maxLength=15, minLength=1 | Número da inscrição municipal |
| `DPS/infDPS/prest/xNome` | 0-1 | TSNomeRazaoSocial | minLength=1, maxLength=300 | Nome/Nome Empresarial do prestador |
| **`DPS/infDPS/prest/end`** | 0-1 | grupo | | Dados de endereço do prestador |
| **`DPS/infDPS/prest/end/endNac`** | 0-1 | grupo | | Grupo de informações específicas de endereço nacional **(escolha)** |
| `DPS/infDPS/prest/end/endNac/cMun` | 1-1 | TSCodMunIBGE | pattern=[0-9]{7} | Código do município, conforme Tabela do IBGE |
| `DPS/infDPS/prest/end/endNac/CEP` | 1-1 | TSCEP | pattern=[0-9]{8} | Número do CEP |
| **`DPS/infDPS/prest/end/endExt`** | 0-1 | grupo | | Grupo de informações específicas de endereço no exterior **(escolha)** |
| `DPS/infDPS/prest/end/endExt/cPais` | 1-1 | TSCodPaisISO | pattern=[A-Z]{2} | Código do país (Tabela de Países ISO) |
| `DPS/infDPS/prest/end/endExt/cEndPost` | 1-1 | TSCodigoEndPostal | minLength=1, maxLength=11 | Código alfanumérico do Endereçamento Postal no exterior do prestador do serviço. |
| `DPS/infDPS/prest/end/endExt/xCidade` | 1-1 | TSCidade | minLength=1, maxLength=60 | Nome da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/prest/end/endExt/xEstProvReg` | 1-1 | TSEstadoProvRegiao | minLength=1, maxLength=60 | Estado, província ou região da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/prest/end/xLgr` | 1-1 | TSLogradouro | minLength=1, maxLength=255 | Tipo e nome do logradouro da localização do imóvel |
| `DPS/infDPS/prest/end/nro` | 1-1 | TSNumeroEndereco | minLength=1, maxLength=60 | Número do imóvel |
| `DPS/infDPS/prest/end/xCpl` | 0-1 | TSComplementoEndereco | minLength=1, maxLength=156 | Complemento do endereço |
| `DPS/infDPS/prest/end/xBairro` | 1-1 | TSBairro | minLength=1, maxLength=60 | Bairro |
| `DPS/infDPS/prest/fone` | 0-1 | TSTelefone | pattern=[0-9]{6,20} | Número do telefone do prestador: Preencher com o Código DDD + número do telefone. Nas operações com exterior é permitido informar o código do país + código da localidade + número do telefone) |
| `DPS/infDPS/prest/email` | 0-1 | TSEmail | minLength=1, maxLength=80 | E-mail |
| **`DPS/infDPS/prest/regTrib`** | 1-1 | grupo | | Grupo de informações relativas aos regimes de tributação do prestador de serviços |
| `DPS/infDPS/prest/regTrib/opSimpNac` | 1-1 | TSOpSimpNac | `1` Não Optante<br>`2` Optante - Microempreendedor Individual (MEI)<br>`3` Optante - Microempresa ou Empresa de Pequeno Porte (ME/EPP)<br>→ `Enums\SimplesNacionalOption` | Situação perante o Simples Nacional: 1 - Não Optante; 2 - Optante - Microempreendedor Individual (MEI); 3 - Optante - Microempresa ou Empresa de Pequeno Porte (ME/EPP); |
| `DPS/infDPS/prest/regTrib/regApTribSN` | 0-1 | TSRegimeApuracaoSimpNac | `1` Regime de apuração dos tributos federais e municipal pelo SN<br>`2` Regime de apuração dos tributos federais pelo SN e ISSQN por fora do SN conforme respectiva legislação municipal do tributo<br>`3` Regime de apuração dos tributos federais e municipal por fora do SN conforme respectivas legislações federal e municipal de cada tributo<br>→ `Enums\SimplesNacionalRegime` | Opção para que o contribuinte optante pelo Simples Nacional ME/EPP (opSimpNac = 3) possa indicar, ao emitir o documento fiscal, em qual regime de apuração os tributos federais e municipal estão inseridos, caso tenha ultrapassado algum sublimite ou limite definido para o Simples Nacional. 1 – Regime de apuração dos tributos federais e municipal pelo SN; 2 – Regime de apuração dos tributos federais pelo SN e ISSQN por fora do SN conforme respectiva legislação municipal do tributo; 3 – Regime de apuração dos tributos federais e municipal por fora do SN conforme respectivas legislações federal e … |
| `DPS/infDPS/prest/regTrib/regEspTrib` | 1-1 | TSRegEspTrib | `0` Nenhum<br>`1` Ato Cooperado (Cooperativa)<br>`2` Estimativa<br>`3` Microempresa Municipal<br>`4` Notário ou Registrador<br>`5` Profissional Autônomo<br>`6` Sociedade de Profissionais<br>`9` Outros<br>→ `Enums\SpecialTaxRegime` | Tipos de Regimes Especiais de Tributação: 0 - Nenhum; 1 - Ato Cooperado (Cooperativa); 2 - Estimativa; 3 - Microempresa Municipal; 4 - Notário ou Registrador; 5 - Profissional Autônomo; 6 - Sociedade de Profissionais; 9 - Outros; |
| **`DPS/infDPS/toma`** | 0-1 | grupo | | Grupo de informações do DPS relativas ao Tomador de Serviços |
| `DPS/infDPS/toma/CNPJ` | 0-1 | TSCNPJ | maxLength=14, pattern=[0-9]{14} | Número do CNPJ **(escolha)** |
| `DPS/infDPS/toma/CPF` | 0-1 | TSCPF | maxLength=11, pattern=[0-9]{11} | Número do CPF **(escolha)** |
| `DPS/infDPS/toma/NIF` | 0-1 | TSNIF | minLength=1, maxLength=40 | Número de Identificação Fiscal fornecido por órgão de administração tributária no exterior **(escolha)** |
| `DPS/infDPS/toma/cNaoNIF` | 0-1 | TSCodNaoNIF | `0` Não informado na nota de origem<br>`1` Dispensado do NIF<br>`2` Não exigência do NIF<br>→ `Enums\NoNifReason` | Motivo para não informação do NIF: 0 - Não informado na nota de origem; 1 - Dispensado do NIF; 2 - Não exigência do NIF; **(escolha)** |
| `DPS/infDPS/toma/CAEPF` | 0-1 | TSCAEPF | maxLength=14, pattern=[0-9]{14} | Número do Cadastro de Atividade Econômica da Pessoa Física (CAEPF) |
| `DPS/infDPS/toma/IM` | 0-1 | TSInscMun | maxLength=15, minLength=1 | Número da inscrição municipal |
| `DPS/infDPS/toma/xNome` | 1-1 | TSNomeRazaoSocial | minLength=1, maxLength=300 | Nome/Nome Empresarial |
| **`DPS/infDPS/toma/end`** | 0-1 | grupo | | Dados de endereço |
| **`DPS/infDPS/toma/end/endNac`** | 0-1 | grupo | | Grupo de informações específicas de endereço nacional **(escolha)** |
| `DPS/infDPS/toma/end/endNac/cMun` | 1-1 | TSCodMunIBGE | pattern=[0-9]{7} | Código do município, conforme Tabela do IBGE |
| `DPS/infDPS/toma/end/endNac/CEP` | 1-1 | TSCEP | pattern=[0-9]{8} | Número do CEP |
| **`DPS/infDPS/toma/end/endExt`** | 0-1 | grupo | | Grupo de informações específicas de endereço no exterior **(escolha)** |
| `DPS/infDPS/toma/end/endExt/cPais` | 1-1 | TSCodPaisISO | pattern=[A-Z]{2} | Código do país (Tabela de Países ISO) |
| `DPS/infDPS/toma/end/endExt/cEndPost` | 1-1 | TSCodigoEndPostal | minLength=1, maxLength=11 | Código alfanumérico do Endereçamento Postal no exterior do prestador do serviço. |
| `DPS/infDPS/toma/end/endExt/xCidade` | 1-1 | TSCidade | minLength=1, maxLength=60 | Nome da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/toma/end/endExt/xEstProvReg` | 1-1 | TSEstadoProvRegiao | minLength=1, maxLength=60 | Estado, província ou região da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/toma/end/xLgr` | 1-1 | TSLogradouro | minLength=1, maxLength=255 | Tipo e nome do logradouro da localização do imóvel |
| `DPS/infDPS/toma/end/nro` | 1-1 | TSNumeroEndereco | minLength=1, maxLength=60 | Número do imóvel |
| `DPS/infDPS/toma/end/xCpl` | 0-1 | TSComplementoEndereco | minLength=1, maxLength=156 | Complemento do endereço |
| `DPS/infDPS/toma/end/xBairro` | 1-1 | TSBairro | minLength=1, maxLength=60 | Bairro |
| `DPS/infDPS/toma/fone` | 0-1 | TSTelefone | pattern=[0-9]{6,20} | Número do telefone do prestador: Preencher com o Código DDD + número do telefone. Nas operações com exterior é permitido informar o código do país + código da localidade + número do telefone) |
| `DPS/infDPS/toma/email` | 0-1 | TSEmail | minLength=1, maxLength=80 | E-mail |
| **`DPS/infDPS/interm`** | 0-1 | grupo | | Grupo de informações do DPS relativas ao Intermediário de Serviços |
| `DPS/infDPS/interm/CNPJ` | 0-1 | TSCNPJ | maxLength=14, pattern=[0-9]{14} | Número do CNPJ **(escolha)** |
| `DPS/infDPS/interm/CPF` | 0-1 | TSCPF | maxLength=11, pattern=[0-9]{11} | Número do CPF **(escolha)** |
| `DPS/infDPS/interm/NIF` | 0-1 | TSNIF | minLength=1, maxLength=40 | Número de Identificação Fiscal fornecido por órgão de administração tributária no exterior **(escolha)** |
| `DPS/infDPS/interm/cNaoNIF` | 0-1 | TSCodNaoNIF | `0` Não informado na nota de origem<br>`1` Dispensado do NIF<br>`2` Não exigência do NIF<br>→ `Enums\NoNifReason` | Motivo para não informação do NIF: 0 - Não informado na nota de origem; 1 - Dispensado do NIF; 2 - Não exigência do NIF; **(escolha)** |
| `DPS/infDPS/interm/CAEPF` | 0-1 | TSCAEPF | maxLength=14, pattern=[0-9]{14} | Número do Cadastro de Atividade Econômica da Pessoa Física (CAEPF) |
| `DPS/infDPS/interm/IM` | 0-1 | TSInscMun | maxLength=15, minLength=1 | Número da inscrição municipal |
| `DPS/infDPS/interm/xNome` | 1-1 | TSNomeRazaoSocial | minLength=1, maxLength=300 | Nome/Nome Empresarial |
| **`DPS/infDPS/interm/end`** | 0-1 | grupo | | Dados de endereço |
| **`DPS/infDPS/interm/end/endNac`** | 0-1 | grupo | | Grupo de informações específicas de endereço nacional **(escolha)** |
| `DPS/infDPS/interm/end/endNac/cMun` | 1-1 | TSCodMunIBGE | pattern=[0-9]{7} | Código do município, conforme Tabela do IBGE |
| `DPS/infDPS/interm/end/endNac/CEP` | 1-1 | TSCEP | pattern=[0-9]{8} | Número do CEP |
| **`DPS/infDPS/interm/end/endExt`** | 0-1 | grupo | | Grupo de informações específicas de endereço no exterior **(escolha)** |
| `DPS/infDPS/interm/end/endExt/cPais` | 1-1 | TSCodPaisISO | pattern=[A-Z]{2} | Código do país (Tabela de Países ISO) |
| `DPS/infDPS/interm/end/endExt/cEndPost` | 1-1 | TSCodigoEndPostal | minLength=1, maxLength=11 | Código alfanumérico do Endereçamento Postal no exterior do prestador do serviço. |
| `DPS/infDPS/interm/end/endExt/xCidade` | 1-1 | TSCidade | minLength=1, maxLength=60 | Nome da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/interm/end/endExt/xEstProvReg` | 1-1 | TSEstadoProvRegiao | minLength=1, maxLength=60 | Estado, província ou região da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/interm/end/xLgr` | 1-1 | TSLogradouro | minLength=1, maxLength=255 | Tipo e nome do logradouro da localização do imóvel |
| `DPS/infDPS/interm/end/nro` | 1-1 | TSNumeroEndereco | minLength=1, maxLength=60 | Número do imóvel |
| `DPS/infDPS/interm/end/xCpl` | 0-1 | TSComplementoEndereco | minLength=1, maxLength=156 | Complemento do endereço |
| `DPS/infDPS/interm/end/xBairro` | 1-1 | TSBairro | minLength=1, maxLength=60 | Bairro |
| `DPS/infDPS/interm/fone` | 0-1 | TSTelefone | pattern=[0-9]{6,20} | Número do telefone do prestador: Preencher com o Código DDD + número do telefone. Nas operações com exterior é permitido informar o código do país + código da localidade + número do telefone) |
| `DPS/infDPS/interm/email` | 0-1 | TSEmail | minLength=1, maxLength=80 | E-mail |
| **`DPS/infDPS/serv`** | 1-1 | grupo | | Grupo de informações do DPS relativas ao Serviço Prestado |
| **`DPS/infDPS/serv/locPrest`** | 1-1 | grupo | | Grupo de informações relativas ao local da prestação do serviço |
| `DPS/infDPS/serv/locPrest/cLocPrestacao` | 1-1 | TSCodMunIBGE | pattern=[0-9]{7} | Código do município onde o serviço foi prestado (tabela do IBGE) |
| `DPS/infDPS/serv/locPrest/cPaisPrestacao` | 1-1 | TSCodPaisISO | pattern=[A-Z]{2} | Código do país onde o serviço foi prestado (Tabela de Países ISO) |
| **`DPS/infDPS/serv/cServ`** | 1-1 | grupo | | Grupo de informações relativas ao código do serviço prestado |
| `DPS/infDPS/serv/cServ/cTribNac` | 1-1 | TSCodTribNac | pattern=[0-9]{6} | Código de tributação nacional do ISSQN, nos termos da LC 116/2003, conforme aba MUN.INCID_INFO.SERV. do ANEXO I Regra de formação - 6 dígitos numéricos sendo: 2 para Item (LC 116/2003), 2 para Subitem (LC 116/2003) e 2 para Desdobro Nacional |
| `DPS/infDPS/serv/cServ/cTribMun` | 0-1 | TCCodTribMun | pattern=[0-9]{3} | Código de tributação municipal do ISSQN |
| `DPS/infDPS/serv/cServ/xDescServ` | 1-1 | TSDesc2000 | minLength=1, maxLength=2000 | Descrição completa do serviço prestado |
| `DPS/infDPS/serv/cServ/cNBS` | 0-1 | TSCodNBS | pattern=[0-9]{9} | Código NBS correspondente ao serviço prestado, seguindo a versão 2.0, conforme Anexo B |
| `DPS/infDPS/serv/cServ/cIntContrib` | 0-1 | TSCodigoInternoContribuinte | minLength=1, maxLength=20, pattern=[a-zA-Z0-9]{1,20} | Código interno do contribuinte |
| **`DPS/infDPS/serv/comExt`** | 0-1 | grupo | | Grupo de informações relativas à exportação/importação de serviço prestado |
| `DPS/infDPS/serv/comExt/mdPrestacao` | 1-1 | TSModoPrestacao | `0` Desconhecido (tipo não informado na nota de origem)<br>`1` Transfronteiriço<br>`2` Consumo no Brasil<br>`3` Presença Comercial no Exterior<br>`4` Movimento Temporário de Pessoas Físicas<br>→ `Enums\ServiceSupplyMode` | Modo de Prestação: 0 - Desconhecido (tipo não informado na nota de origem); 1 - Transfronteiriço; 2 - Consumo no Brasil; 3 - Movimento Temporário de Pessoas Físicas; 4 - Consumo no Exterior; |
| `DPS/infDPS/serv/comExt/vincPrest` | 1-1 | TSVincPrest | `0` Sem vínculo com o Tomador/Prestador<br>`1` Controlada<br>`2` Controladora<br>`3` Coligada<br>`4` Matriz<br>`5` Filial ou sucursal<br>`6` Outro vínculo<br>`9` Desconhecido (tipo não informado na nota de origem)<br>→ `Enums\ForeignTradeLink` | Vínculo entre as partes no negócio: 0 - Sem vínculo com o Tomador/Prestador 1 - Controlada; 2 - Controladora; 3 - Coligada; 4 - Matriz; 5 - Filial ou sucursal; 6 - Outro vínculo; 9 - Desconhecido (tipo não informado na nota de origem); |
| `DPS/infDPS/serv/comExt/tpMoeda` | 1-1 | TSCodMoeda | maxLength=3, pattern=[0-9]{3} | Identifica a moeda da transação comercial |
| `DPS/infDPS/serv/comExt/vServMoeda` | 1-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor do serviço prestado expresso em moeda estrangeira especificada em tpmoeda |
| `DPS/infDPS/serv/comExt/mecAFComexP` | 1-1 | TSMecAFComExPrest | `00` Desconhecido (tipo não informado na nota de origem)<br>`01` Nenhum<br>`02` ACC - Adiantamento sobre Contrato de Câmbio – Redução a Zero do IR e do IOF<br>`03` ACE – Adiantamento sobre Cambiais Entregues - Redução a Zero do IR e do IOF<br>`04` BNDES-Exim Pós-Embarque – Serviços<br>`05` BNDES-Exim Pré-Embarque - Serviços<br>`06` FGE - Fundo de Garantia à Exportação<br>`07` PROEX - EQUALIZAÇÃO<br>`08` PROEX - Financiamento<br>→ `Enums\ProviderSupportMechanism` | Mecanismo de apoio/fomento ao Comércio Exterior utilizado pelo prestador do serviço: 00 - Desconhecido (tipo não informado na nota de origem); 01 - Nenhum; 02 - ACC - Adiantamento sobre Contrato de Câmbio – Redução a Zero do IR e do IOF; 03 - ACE – Adiantamento sobre Cambiais Entregues - Redução a Zero do IR e do IOF; 04 - BNDES-Exim Pós-Embarque – Serviços; 05 - BNDES-Exim Pré-Embarque - Serviços; 06 - FGE - Fundo de Garantia à Exportação; 07 - PROEX - EQUALIZAÇÃO 08 - PROEX - Financiamento; |
| `DPS/infDPS/serv/comExt/mecAFComexT` | 1-1 | TSMecAFComExToma | `00` Desconhecido (tipo não informado na nota de origem)<br>`01` Nenhum<br>`02` Adm. Pública e Repr. Internacional<br>`03` Alugueis e Arrend. Mercantil de maquinas, equip., embarc. e aeronaves<br>`04` Arrendamento Mercantil de aeronave para empresa de transporte aéreo público<br>`05` Comissão a agentes externos na exportação<br>`06` Despesas de armazenagem, mov. e transporte de carga no exterior<br>`07` Eventos FIFA (subsidiária)<br>`08` Eventos FIFA<br>`09` Fretes, arrendamentos de embarcações ou aeronaves e outros<br>`10` Material Aeronáutico<br>`11` Promoção de Bens no Exterior<br>`12` Pro… | Mecanismo de apoio/fomento ao Comércio Exterior utilizado pelo tomador do serviço: 00 - Desconhecido (tipo não informado na nota de origem); 01 - Nenhum; 02 - Adm. Pública e Repr. Internacional; 03 - Alugueis e Arrend. Mercantil de maquinas, equip., embarc. e aeronaves; 04 - Arrendamento Mercantil de aeronave para empresa de transporte aéreo público; 05 - Comissão a agentes externos na exportação; 06 - Despesas de armazenagem, mov. e transporte de carga no exterior; 07 - Eventos FIFA (subsidiária); 08 - Eventos FIFA; 09 - Fretes, arrendamentos de embarcações ou aeronaves e outros; 10 - Materi… |
| `DPS/infDPS/serv/comExt/movTempBens` | 1-1 | TSMovTempBens | `0` Desconhecido (tipo não informado na nota de origem)<br>`1` Não<br>`2` Vinculada - Declaração de Importação<br>`3` Vinculada - Declaração de Exportação<br>→ `Enums\TemporaryGoodsMovement` | Vínculo da Operação à Movimentação Temporária de Bens: 0 - Desconhecido (tipo não informado na nota de origem); 1 - Não; 2 - Vinculada - Declaração de Importação; 3 - Vinculada - Declaração de Exportação; |
| `DPS/infDPS/serv/comExt/nDI` | 0-1 | TSNumDocImport | minLength=1, maxLength=12 | Número da Declaração de Importação (DI/DSI/DA/DRI-E) averbado |
| `DPS/infDPS/serv/comExt/nRE` | 0-1 | TSNumRegExport | minLength=1, maxLength=12 | Número do Registro de Exportação (RE) averbado |
| `DPS/infDPS/serv/comExt/mdic` | 1-1 | TSEnvMDIC | `0` Não enviar para o MDIC<br>`1` Enviar para o MDIC<br>→ `Enums\MdicSubmission` | Compartilhar as informações da NFS-e gerada a partir desta DPS com a Secretaria de Comércio Exterior: 0 - Não enviar para o MDIC; 1 - Enviar para o MDIC; |
| **`DPS/infDPS/serv/obra`** | 0-1 | grupo | | Grupo de informações do DPS relativas à serviço de obra |
| `DPS/infDPS/serv/obra/inscImobFisc` | 0-1 | TSInscImobFisc | minLength=1, maxLength=30 | Inscrição imobiliária fiscal (código fornecido pela Prefeitura Municipal para a identificação da obra ou para fins de recolhimento do IPTU) |
| `DPS/infDPS/serv/obra/cObra` | 0-1 | TSCodObra | minLength=1, maxLength=30 | Número de identificação da obra. Cadastro Nacional de Obras (CNO) ou Cadastro Específico do INSS (CEI). **(escolha)** |
| `DPS/infDPS/serv/obra/cCIB` | 0-1 | TSCodCIB | length=8 | Código do Cadastro Imobiliário Brasileiro - CIB. **(escolha)** |
| **`DPS/infDPS/serv/obra/end`** | 0-1 | grupo | | Grupo de informações do endereço da obra do serviço prestado **(escolha)** |
| `DPS/infDPS/serv/obra/end/CEP` | 0-1 | TSCEP | pattern=[0-9]{8} | Número do CEP **(escolha)** |
| **`DPS/infDPS/serv/obra/end/endExt`** | 0-1 | grupo | | Grupo de informações específicas de endereço no exterior **(escolha)** |
| `DPS/infDPS/serv/obra/end/endExt/cEndPost` | 1-1 | TSCodigoEndPostal | minLength=1, maxLength=11 | Código alfanumérico do Endereçamento Postal no exterior do prestador do serviço. |
| `DPS/infDPS/serv/obra/end/endExt/xCidade` | 1-1 | TSCidade | minLength=1, maxLength=60 | Nome da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/serv/obra/end/endExt/xEstProvReg` | 1-1 | TSEstadoProvRegiao | minLength=1, maxLength=60 | Estado, província ou região da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/serv/obra/end/xLgr` | 1-1 | TSLogradouro | minLength=1, maxLength=255 | Tipo e nome do logradouro da localização do imóvel |
| `DPS/infDPS/serv/obra/end/nro` | 1-1 | TSNumeroEndereco | minLength=1, maxLength=60 | Número do imóvel |
| `DPS/infDPS/serv/obra/end/xCpl` | 0-1 | TSComplementoEndereco | minLength=1, maxLength=156 | Complemento do endereço |
| `DPS/infDPS/serv/obra/end/xBairro` | 1-1 | TSBairro | minLength=1, maxLength=60 | Bairro |
| **`DPS/infDPS/serv/atvEvento`** | 0-1 | grupo | | Grupo de informações do DPS relativas à Evento |
| `DPS/infDPS/serv/atvEvento/xNome` | 1-1 | TSDesc255 | minLength=1, maxLength=255 | Descrição do evento Artístico, Cultural, Esportivo, etc |
| `DPS/infDPS/serv/atvEvento/dtIni` | 1-1 | TSData | pattern=(((20(([02468][048])\|([13579][26]))-02-29))\|(20[0-9][0-9])-((((0[1-9])\|(1[0-2]))-((0[1-9])\|(1\d)\|(2[0-8])))\|((((0[13578])\|(1[02]))-31)\|(((0[1,3-9])\|(1[0-2]))-(29\|30))))) | Data de início da atividade de evento. Ano, Mês e Dia (AAAA-MM-DD) |
| `DPS/infDPS/serv/atvEvento/dtFim` | 1-1 | TSData | pattern=(((20(([02468][048])\|([13579][26]))-02-29))\|(20[0-9][0-9])-((((0[1-9])\|(1[0-2]))-((0[1-9])\|(1\d)\|(2[0-8])))\|((((0[13578])\|(1[02]))-31)\|(((0[1,3-9])\|(1[0-2]))-(29\|30))))) | Data de fim da atividade de evento. Ano, Mês e Dia (AAAA-MM-DD) |
| `DPS/infDPS/serv/atvEvento/idAtvEvt` | 0-1 | TSIdeEvento | minLength=1, maxLength=30 | Identificação da Atividade de Evento (código identificador de evento determinado pela Administração Tributária Municipal) **(escolha)** |
| **`DPS/infDPS/serv/atvEvento/end`** | 0-1 | grupo | | Grupo de informações relativas ao endereço da atividade, evento ou local do serviço prestado **(escolha)** |
| `DPS/infDPS/serv/atvEvento/end/CEP` | 0-1 | TSCEP | pattern=[0-9]{8} | Número do CEP **(escolha)** |
| **`DPS/infDPS/serv/atvEvento/end/endExt`** | 0-1 | grupo | | Grupo de informações específicas de endereço no exterior **(escolha)** |
| `DPS/infDPS/serv/atvEvento/end/endExt/cEndPost` | 1-1 | TSCodigoEndPostal | minLength=1, maxLength=11 | Código alfanumérico do Endereçamento Postal no exterior do prestador do serviço. |
| `DPS/infDPS/serv/atvEvento/end/endExt/xCidade` | 1-1 | TSCidade | minLength=1, maxLength=60 | Nome da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/serv/atvEvento/end/endExt/xEstProvReg` | 1-1 | TSEstadoProvRegiao | minLength=1, maxLength=60 | Estado, província ou região da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/serv/atvEvento/end/xLgr` | 1-1 | TSLogradouro | minLength=1, maxLength=255 | Tipo e nome do logradouro da localização do imóvel |
| `DPS/infDPS/serv/atvEvento/end/nro` | 1-1 | TSNumeroEndereco | minLength=1, maxLength=60 | Número do imóvel |
| `DPS/infDPS/serv/atvEvento/end/xCpl` | 0-1 | TSComplementoEndereco | minLength=1, maxLength=156 | Complemento do endereço |
| `DPS/infDPS/serv/atvEvento/end/xBairro` | 1-1 | TSBairro | minLength=1, maxLength=60 | Bairro |
| **`DPS/infDPS/serv/infoCompl`** | 0-1 | grupo | | Grupo de informações complementares disponível para todos os serviços prestados |
| `DPS/infDPS/serv/infoCompl/idDocTec` | 0-1 | TSDRT | minLength=1, maxLength=40 | Identificador de Documento de Responsabilidade Técnica: ART, RRT, DRT, Outros. |
| `DPS/infDPS/serv/infoCompl/docRef` | 0-1 | TSDesc255 | minLength=1, maxLength=255 | Chave da nota, número identificador da nota, número do contrato ou outro identificador de documento emitido pelo prestador de serviços, que subsidia a emissão dessa nota pelo tomador do serviço ou intermediário (preenchimento obrigatório caso a nota esteja sendo emitida pelo Tomador ou intermediário do serviço). |
| `DPS/infDPS/serv/infoCompl/xPed` | 0-1 | TSNumeroEndereco | minLength=1, maxLength=60 | Número do pedido/ordem de compra/ordem de serviço/projeto que autorize a prestação do serviço em operações B2B - Informação de interesse do tomador do serviço para controle e gestão da Negociação |
| **`DPS/infDPS/serv/infoCompl/gItemPed`** | 0-1 | grupo | | Grupo de itens do pedido/ordem de compra/ordem de serviço/projeto |
| `DPS/infDPS/serv/infoCompl/gItemPed/xItemPed` | 1-99 | TSNumeroEndereco | minLength=1, maxLength=60 | Número do item do pedido/ordem de compra/ordem de serviço/projeto - Identificação do número do item do pedido ou ordem de compra destacado e xPed |
| `DPS/infDPS/serv/infoCompl/xInfComp` | 0-1 | TSDescInfCompl | minLength=1, maxLength=2000 | Informações complementares |
| **`DPS/infDPS/valores`** | 1-1 | grupo | | Grupo de informações relativas à valores do serviço prestado |
| **`DPS/infDPS/valores/vServPrest`** | 1-1 | grupo | | Grupo de informações relativas aos valores do serviço prestado |
| `DPS/infDPS/valores/vServPrest/vReceb` | 0-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor monetário recebido pelo intermediário do serviço (R$) |
| `DPS/infDPS/valores/vServPrest/vServ` | 1-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor dos serviços em R$ |
| **`DPS/infDPS/valores/vDescCondIncond`** | 0-1 | grupo | | Grupo de informações relativas aos descontos condicionados e incondicionados |
| `DPS/infDPS/valores/vDescCondIncond/vDescIncond` | 0-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor monetário do desconto incondicionado (R$) |
| `DPS/infDPS/valores/vDescCondIncond/vDescCond` | 0-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor monetário do desconto condicionado (R$) |
| **`DPS/infDPS/valores/vDedRed`** | 0-1 | grupo | | Grupo de informações relativas ao valores para dedução/redução do valor da base de cálculo (valor do serviço) |
| `DPS/infDPS/valores/vDedRed/pDR` | 0-1 | TSDec3V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,2}(\.[0-9]{2})? | Valor percentual padrão para dedução/redução do valor do serviço **(escolha)** |
| `DPS/infDPS/valores/vDedRed/vDR` | 0-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor monetário padrão para dedução/redução do valor do serviço **(escolha)** |
| **`DPS/infDPS/valores/vDedRed/documentos`** | 0-1 | grupo | | Grupo de informações de documento utilizado para Dedução/Redução do valor do serviço **(escolha)** |
| **`DPS/infDPS/valores/vDedRed/documentos/docDedRed`** | 1-1000 | grupo | | Grupo de informações de documento utilizado para Dedução/Redução do valor do serviço |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/chNFSe` | 0-1 | TSChaveNFSe | maxLength=50, pattern=[0-9]{50} | Chave de Acesso da NFS-e (Padrão Nacional) **(escolha)** |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/chNFe` | 0-1 | TSChaveNFe | maxLength=44, pattern=[0-9]{44} | Chave de Acesso da NF-e **(escolha)** |
| **`DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFSeMun`** | 0-1 | grupo | | Grupo de informações de Outras NFS-e (Padrão anterior de NFS-e) **(escolha)** |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFSeMun/cMunNFSeMun` | 1-1 | TSCodMunIBGE | pattern=[0-9]{7} | Código Município emissor da nota eletrônica municipal (Tabela do IBGE) |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFSeMun/nNFSeMun` | 1-1 | TSNum15Dig | maxLength=15, pattern=[0-9]{15} | Número da nota eletrônica municipal |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFSeMun/cVerifNFSeMun` | 1-1 | TSCodVerificacao | minLength=1, maxLength=9, pattern=[a-zA-Z0-9]{1,9} | Código de Verificação da nota eletrônica municipal |
| **`DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFNFS`** | 0-1 | grupo | | Grupo de informações de NF ou NFS (Modelo não eletrônico) **(escolha)** |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFNFS/nNFS` | 1-1 | TSNum7Dig | maxLength=7, pattern=[0-9]{7} | Número da Nota Fiscal NF ou NFS |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFNFS/modNFS` | 1-1 | TSNum15Dig | maxLength=15, pattern=[0-9]{15} | Modelo da Nota Fiscal NF ou NFS |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/NFNFS/serieNFS` | 1-1 | TSSerieNFNFS | minLength=1, maxLength=15, pattern=[a-zA-Z0-9]{1,15} | Série Nota Fiscal NF ou NFS |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/nDocFisc` | 0-1 | TSDesc255 | minLength=1, maxLength=255 | Número de documento fiscal **(escolha)** |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/nDoc` | 0-1 | TSDesc255 | minLength=1, maxLength=255 | Número de documento não fiscal **(escolha)** |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/tpDedRed` | 1-1 | TSIdeDedRed | `1` Alimentação e bebidas/frigobar<br>`2` Materiais<br>`3` Produção Externa<br>`4` Reembolso de despesas<br>`5` Repasse consorciado<br>`6` Repasse plano de saúde<br>`7` Serviços<br>`8` Subempreitada de mão de obra<br>`9` Profissional parceiro<br>`99` Outras deduções<br>→ `Enums\DeductionType` | Identificação da Dedução/Redução: 1 – Alimentação e bebidas/frigobar; 2 – Materiais; 3 - Produção Externa; 4 - Reembolso de despesas; 5 – Repasse consorciado; 6 – Repasse plano de saúde; 7 – Serviços; 8 – Subempreitada de mão de obra; 9 - Profissional parceiro; 99 – Outras deduções; |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/xDescOutDed` | 0-1 | TSDescOutDedRed | minLength=1, maxLength=150 | Descrição da Dedução/Redução quando a opção é "99 – Outras Deduções" |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/dtEmiDoc` | 1-1 | xs:date |  | Data da emissão do documento dedutível. Ano, mês e dia (AAAA-MM-DD) |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/vDedutivelRedutivel` | 1-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor monetário total dedutível/redutível no documento informado (R$). Este é o valor total no documento informado que é passível de dedução/redução. |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/vDeducaoReducao` | 1-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor monetário utilizado para dedução/redução do valor do serviço da NFS-e que está sendo emitida (R$). Deve ser menor ou igual ao valor deduzível/redutível (vDedutivelRedutivel). |
| **`DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec`** | 0-1 | grupo | | Grupo de informações do Fornecedor em Deduções de Serviços |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/CNPJ` | 0-1 | TSCNPJ | maxLength=14, pattern=[0-9]{14} | Número do CNPJ **(escolha)** |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/CPF` | 0-1 | TSCPF | maxLength=11, pattern=[0-9]{11} | Número do CPF **(escolha)** |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/NIF` | 0-1 | TSNIF | minLength=1, maxLength=40 | Número de Identificação Fiscal fornecido por órgão de administração tributária no exterior **(escolha)** |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/cNaoNIF` | 0-1 | TSCodNaoNIF | `0` Não informado na nota de origem<br>`1` Dispensado do NIF<br>`2` Não exigência do NIF<br>→ `Enums\NoNifReason` | Motivo para não informação do NIF: 0 - Não informado na nota de origem; 1 - Dispensado do NIF; 2 - Não exigência do NIF; **(escolha)** |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/CAEPF` | 0-1 | TSCAEPF | maxLength=14, pattern=[0-9]{14} | Número do Cadastro de Atividade Econômica da Pessoa Física (CAEPF) |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/IM` | 0-1 | TSInscMun | maxLength=15, minLength=1 | Número da inscrição municipal |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/xNome` | 1-1 | TSNomeRazaoSocial | minLength=1, maxLength=300 | Nome/Nome Empresarial |
| **`DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end`** | 0-1 | grupo | | Dados de endereço |
| **`DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endNac`** | 0-1 | grupo | | Grupo de informações específicas de endereço nacional **(escolha)** |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endNac/cMun` | 1-1 | TSCodMunIBGE | pattern=[0-9]{7} | Código do município, conforme Tabela do IBGE |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endNac/CEP` | 1-1 | TSCEP | pattern=[0-9]{8} | Número do CEP |
| **`DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endExt`** | 0-1 | grupo | | Grupo de informações específicas de endereço no exterior **(escolha)** |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endExt/cPais` | 1-1 | TSCodPaisISO | pattern=[A-Z]{2} | Código do país (Tabela de Países ISO) |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endExt/cEndPost` | 1-1 | TSCodigoEndPostal | minLength=1, maxLength=11 | Código alfanumérico do Endereçamento Postal no exterior do prestador do serviço. |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endExt/xCidade` | 1-1 | TSCidade | minLength=1, maxLength=60 | Nome da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/endExt/xEstProvReg` | 1-1 | TSEstadoProvRegiao | minLength=1, maxLength=60 | Estado, província ou região da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/xLgr` | 1-1 | TSLogradouro | minLength=1, maxLength=255 | Tipo e nome do logradouro da localização do imóvel |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/nro` | 1-1 | TSNumeroEndereco | minLength=1, maxLength=60 | Número do imóvel |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/xCpl` | 0-1 | TSComplementoEndereco | minLength=1, maxLength=156 | Complemento do endereço |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/end/xBairro` | 1-1 | TSBairro | minLength=1, maxLength=60 | Bairro |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/fone` | 0-1 | TSTelefone | pattern=[0-9]{6,20} | Número do telefone do prestador: Preencher com o Código DDD + número do telefone. Nas operações com exterior é permitido informar o código do país + código da localidade + número do telefone) |
| `DPS/infDPS/valores/vDedRed/documentos/docDedRed/fornec/email` | 0-1 | TSEmail | minLength=1, maxLength=80 | E-mail |
| **`DPS/infDPS/valores/trib`** | 1-1 | grupo | | Grupo de informações relacionados aos tributos relacionados ao serviço prestado |
| **`DPS/infDPS/valores/trib/tribMun`** | 1-1 | grupo | | Grupo de informações relacionados ao Imposto Sobre Serviços de Qualquer Natureza - ISSQN |
| `DPS/infDPS/valores/trib/tribMun/tribISSQN` | 1-1 | TSTribISSQN | `1` Operação tributável<br>`2` Imunidade<br>`3` Exportação de serviço<br>`4` Não Incidência<br>→ `Enums\IssqnTaxation` | Tributação do ISSQN sobre o serviço prestado: 1 - Operação tributável; 2 - Imunidade; 3 - Exportação de serviço; 4 - Não Incidência; |
| `DPS/infDPS/valores/trib/tribMun/cPaisResult` | 0-1 | TSCodPaisISO | pattern=[A-Z]{2} | Código do país onde se verficou o resultado da prestação do serviço para o caso de Exportação de Serviço.(Tabela de Países ISO) |
| `DPS/infDPS/valores/trib/tribMun/tpImunidade` | 0-1 | TSTipoImunidadeISSQN | `0` Imunidade (tipo não informado na nota de origem)<br>`1` Patrimônio, renda ou serviços, uns dos outros (CF88, Art 150, VI, a)<br>`2` Templos de qualquer culto (CF88, Art 150, VI, b)<br>`3` Patrimônio, renda ou serviços dos partidos políticos, inclusive suas fundações, das entidades sindicais dos trabalhadores, das instituições de educação e de assistência social, sem fins lucrativos, atendidos os requisitos da lei (CF88, Art 150, VI, c)<br>`4` Livros, jornais, periódicos e o papel destinado a sua impressão (CF88, Art 150, VI, d)<br>`5` Fonogramas e videofonogramas musicais produzidos no Br… | Identificação da Imunidade do ISSQN – somente para o caso de Imunidade. Tipos de Imunidades: 0 - Imunidade (tipo não informado na nota de origem); 1 - Patrimônio, renda ou serviços, uns dos outros (CF88, Art 150, VI, a); 2 - Templos de qualquer culto (CF88, Art 150, VI, b); 3 - Patrimônio, renda ou serviços dos partidos políticos, inclusive suas fundações, das entidades sindicais dos trabalhadores, das instituições de educação e de assistência social, sem fins lucrativos, atendidos os requisitos da lei (CF88, Art 150, VI, c); 4 - Livros, jornais, periódicos e o papel destinado a sua impressão… |
| **`DPS/infDPS/valores/trib/tribMun/exigSusp`** | 0-1 | grupo | | Informações para a suspensão da Exigibilidade do ISSQN |
| `DPS/infDPS/valores/trib/tribMun/exigSusp/tpSusp` | 1-1 | TSOpExigSuspensa | `1` Exigibilidade Suspensa por Decisão Judicial<br>`2` Exigibilidade Suspensa por Processo Administrativo<br>→ `Enums\IssqnSuspension` | Opção para Exigibilidade Suspensa: 1 - Exigibilidade Suspensa por Decisão Judicial; 2 - Exigibilidade Suspensa por Processo Administrativo; |
| `DPS/infDPS/valores/trib/tribMun/exigSusp/nProcesso` | 1-1 | TSNumProcExigSuspensa | pattern=[0-9]{30} | Número do processo judicial ou administrativo de suspensão da exigibilidade |
| **`DPS/infDPS/valores/trib/tribMun/BM`** | 0-1 | grupo | | Tributação do ISSQN sobre o serviço prestado: 1 - Operação tributável; 2 - Exportação de serviço; 3 - Não Incidência; 4 - Imunidade; |
| `DPS/infDPS/valores/trib/tribMun/BM/nBM` | 1-1 | TSNumBeneficioMunicipal | pattern=[0-9]{14} | Identificador do benefício parametrizado pelo município. Trata-se de um identificador único que foi gerado pelo Sistema Nacional no momento em que o município de incidência do ISSQN incluiu o benefício no sistema. Critério de formação do número de identificação de parâmetros municipais: 7 dígitos - posição 1 a 7: número identificador do Município, conforme código IBGE; 2 dígitos - posições 8 e 9 : número identificador do tipo de parametrização (01-legislação, 02-regimes especiais, 03-retenções, 04-outros benefícios); 5 dígitos - posição 10 a 14 : número sequencial definido pelo sistema quando… |
| `DPS/infDPS/valores/trib/tribMun/BM/vRedBCBM` | 0-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor monetário informado pelo emitente para redução da base de cálculo (BC) do ISSQN devido a um Benefício Municipal (BM). **(escolha)** |
| `DPS/infDPS/valores/trib/tribMun/BM/pRedBCBM` | 0-1 | TSDec3V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,2}(\.[0-9]{2})? | Valor percentual informado pelo emitente para redução da base de cálculo (BC) do ISSQN devido a um Benefício Municipal (BM). **(escolha)** |
| `DPS/infDPS/valores/trib/tribMun/tpRetISSQN` | 1-1 | TSTipoRetISSQN | `1` Não Retido<br>`2` Retido pelo Tomador<br>`3` Retido pelo Intermediario<br>→ `Enums\IssqnWithholding` | Tipo de retencao do ISSQN: 1 - Não Retido; 2 - Retido pelo Tomador; 3 - Retido pelo Intermediario; |
| `DPS/infDPS/valores/trib/tribMun/pAliq` | 0-1 | TSDec1V2 | pattern=0\|[0-9]{1}(\.[0-9]{2})? | Valor da alíquota (%) do serviço prestado relativo ao município sujeito ativo (município de incidência) do ISSQN. Se o município de incidência pertence ao Sistema Nacional NFS-e a alíquota estará parametrizada e, portanto, será fornecida pelo sistema. Se o município de incidência não pertence ao Sistema Nacional NFS-e a alíquota não estará parametrizada e, por isso, deverá ser fornecida pelo emitente. |
| **`DPS/infDPS/valores/trib/tribFed`** | 0-1 | grupo | | Grupo de informações de outros tributos relacionados ao serviço prestado |
| **`DPS/infDPS/valores/trib/tribFed/piscofins`** | 0-1 | grupo | | Grupo de informações dos tributos PIS/COFINS |
| `DPS/infDPS/valores/trib/tribFed/piscofins/CST` | 1-1 | TSTipoCST | `00` Nenhum<br>`01` Operação Tributável com Alíquota Básica<br>`02` Operação Tributável com Alíquota Diferenciada<br>`03` Operação Tributável com Alíquota por Unidade de Medida de Produto<br>`04` Operação Tributável monofásica - Revenda a Alíquota Zero<br>`05` Operação Tributável por Substituição Tributária<br>`06` Operação Tributável a Alíquota Zero<br>`07` Operação Isenta da Contribuição<br>`08` Operação sem Incidência da Contribuição<br>`09` Operação com Suspensão da Contribuição<br>`49` Outras Operações de Saída<br>`50` Operação com Direito a Crédito – Vinculada Exclusivamente a Receita T… | Código de Situação Tributária do PIS/COFINS (CST): 00 - Nenhum; 01 - Operação Tributável com Alíquota Básica; 02 - Operação Tributável com Alíquota Diferenciada; 03 - Operação Tributável com Alíquota por Unidade de Medida de Produto; 04 - Operação Tributável monofásica - Revenda a Alíquota Zero; 05 - Operação Tributável por Substituição Tributária; 06 - Operação Tributável a Alíquota Zero; 07 - Operação Isenta da Contribuição; 08 - Operação sem Incidência da Contribuição; 09 - Operação com Suspensão da Contribuição; 49 - Outras Operações de Saída; 50 - Operação com Direito a Crédito – Vincula… |
| `DPS/infDPS/valores/trib/tribFed/piscofins/vBCPisCofins` | 0-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor da Base de Cálculo do PIS/COFINS, relativo à apuração própria (R$). |
| `DPS/infDPS/valores/trib/tribFed/piscofins/pAliqPis` | 0-1 | TSDec2V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,1}(\.[0-9]{2})? | Alíquota do PIS, relativa à apuração própria (%). |
| `DPS/infDPS/valores/trib/tribFed/piscofins/pAliqCofins` | 0-1 | TSDec2V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,1}(\.[0-9]{2})? | Alíquota da COFINS, relativa à apuração própria (%). |
| `DPS/infDPS/valores/trib/tribFed/piscofins/vPis` | 0-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor do débito de PIS apuração própria (R$). |
| `DPS/infDPS/valores/trib/tribFed/piscofins/vCofins` | 0-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor do débito de COFINS apuração própria (R$). |
| `DPS/infDPS/valores/trib/tribFed/piscofins/tpRetPisCofins` | 0-1 | TSTipoRetPISCofins | `0` PIS/COFINS/CSLL Não Retidos<br>`1` PIS/COFINS Retidos<br>`2` PIS/COFINS Não Retidos<br>`3` PIS/COFINS/CSLL Retidos<br>`4` PIS/COFINS Retidos, CSLL Não Retido<br>`5` PIS Retido, COFINS/CSLL Não Retido<br>`6` COFINS Retido, PIS/CSLL Não Retido<br>`7` PIS Não Retido, COFINS/CSLL Retidos<br>`8` PIS/COFINS Não Retidos, CSLL Retido<br>`9` COFINS Não Retido, PIS/CSLL Retidos<br>→ `Enums\FederalWithholding` | Tipo de retenção do PIS/COFINS: 0 - PIS/COFINS/CSLL Não Retidos; 1 - PIS/COFINS Retidos; 2 - PIS/COFINS Não Retidos; 3 - PIS/COFINS/CSLL Retidos; 4 - PIS/COFINS Retidos, CSLL Não Retido; 5 - PIS Retido, COFINS/CSLL Não Retido; 6 - COFINS Retido, PIS/CSLL Não Retido; 7 - PIS Não Retido, COFINS/CSLL Retidos; 8 - PIS/COFINS Não Retidos, CSLL Retido; 9 - COFINS Não Retido, PIS/CSLL Retidos; |
| `DPS/infDPS/valores/trib/tribFed/vRetCP` | 0-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor monetário do CP(R$). |
| `DPS/infDPS/valores/trib/tribFed/vRetIRRF` | 0-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor monetário do IRRF (R$). |
| `DPS/infDPS/valores/trib/tribFed/vRetCSLL` | 0-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor monetário do CSLL (R$). |
| **`DPS/infDPS/valores/trib/totTrib`** | 1-1 | grupo | | Grupo de informações para totais aproximados dos tributos relacionados ao serviço prestado |
| **`DPS/infDPS/valores/trib/totTrib/vTotTrib`** | 0-1 | grupo | | Valor monetário total aproximado dos tributos, em conformidade com o artigo 1o da Lei no 12.741/2012 **(escolha)** |
| `DPS/infDPS/valores/trib/totTrib/vTotTrib/vTotTribFed` | 1-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor monetário total aproximado dos tributos federais (R$). |
| `DPS/infDPS/valores/trib/totTrib/vTotTrib/vTotTribEst` | 1-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor monetário total aproximado dos tributos estaduais (R$). |
| `DPS/infDPS/valores/trib/totTrib/vTotTrib/vTotTribMun` | 1-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor monetário total aproximado dos tributos municipais (R$). |
| **`DPS/infDPS/valores/trib/totTrib/pTotTrib`** | 0-1 | grupo | | Valor percentual total aproximado dos tributos, em conformidade com o artigo 1o da Lei no 12.741/2012 **(escolha)** |
| `DPS/infDPS/valores/trib/totTrib/pTotTrib/pTotTribFed` | 1-1 | TSDec3V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,2}(\.[0-9]{2})? | Valor percentual total aproximado dos tributos federais (%). |
| `DPS/infDPS/valores/trib/totTrib/pTotTrib/pTotTribEst` | 1-1 | TSDec3V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,2}(\.[0-9]{2})? | Valor percentual total aproximado dos tributos estaduais (%). |
| `DPS/infDPS/valores/trib/totTrib/pTotTrib/pTotTribMun` | 1-1 | TSDec3V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,2}(\.[0-9]{2})? | Valor percentual total aproximado dos tributos municipais (%). |
| `DPS/infDPS/valores/trib/totTrib/indTotTrib` | 0-1 | TSTipoIndTotTrib | `0` Não<br>→ `Enums\TotalTaxIndicator` | Indicador de informação de valor total de tributos. Possui valor fixo igual a zero (indTotTrib=0). Não informar nenhum valor estimado para os Tributos (Decreto 8.264/2014). 0 - Não; **(escolha)** |
| `DPS/infDPS/valores/trib/totTrib/pTotTribSN` | 0-1 | TSDec2V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,1}(\.[0-9]{2})? | Valor percentual aproximado do total dos tributos da alíquota do Simples Nacional (%) **(escolha)** |
| **`DPS/infDPS/IBSCBS`** | 0-1 | grupo | | Grupo de informações declaradas pelo emitente referentes ao IBS e à CBS |
| `DPS/infDPS/IBSCBS/finNFSe` | 1-1 | TSRTCFinNFSe | `0` NFS-e regular<br>→ `Enums\NfsePurpose` | Indicador da finalidade da emissão de NFS-e |
| `DPS/infDPS/IBSCBS/indFinal` | 0-1 | TSRTCIndFinal | `0` Não<br>`1` Sim<br>→ `Enums\FinalConsumer` | Indica operação de uso ou consumo pessoal (art. 57) |
| `DPS/infDPS/IBSCBS/cIndOp` | 1-1 | TSRTCCodIndOp | pattern=[0-9]{6} | Código indicador da operação de fornecimento, conforme tabela "código indicador de operação" |
| `DPS/infDPS/IBSCBS/tpOper` | 0-1 | TSRTCTpOper | `1` Fornecimento com pagamento posterior<br>`2` Recebimento do pagamento com fornecimento já realizado<br>`3` Fornecimento com pagamento já realizado<br>`4` Recebimento do pagamento com fornecimento posterior<br>`5` Fornecimento e recebimento do pagamento concomitantes<br>→ `Enums\GovernmentOperationType` | Tipo de Operação com Entes Governamentais ou outros serviços sobre bens imóveis |
| **`DPS/infDPS/IBSCBS/gRefNFSe`** | 0-1 | grupo | | Grupo de NFS-e referenciadas |
| `DPS/infDPS/IBSCBS/gRefNFSe/refNFSe` | 1-99 | TSChaveNFSe | maxLength=50, pattern=[0-9]{50} | Chave da NFS-e referenciada |
| `DPS/infDPS/IBSCBS/tpEnteGov` | 0-1 | TSRTCTpEnteGov | `1` União<br>`2` Estado<br>`3` Distrito Federal<br>`4` Município<br>→ `Enums\GovernmentEntity` | Tipo de ente governamental |
| `DPS/infDPS/IBSCBS/indDest` | 1-1 | TSRTCIndDest | `0` o destinatário é o próprio tomador/adquirente identificado na NFS-e (tomador = adquirente = destinatário)<br>`1` o destinatário não é o próprio adquirente, podendo ser outra pessoa, física ou jurídica (ou equiparada), ou um estabelecimento diferente do indicado como tomador (tomador = adquirente ≠ destinatário)<br>→ `Enums\RecipientIndicator` | A respeito do Destinatário dos serviços |
| **`DPS/infDPS/IBSCBS/dest`** | 0-1 | grupo | | Grupo de informações relativas ao Destinatário |
| `DPS/infDPS/IBSCBS/dest/CNPJ` | 0-1 | TSCNPJ | maxLength=14, pattern=[0-9]{14} | Número da inscrição no Cadastro Nacional de Pessoa Jurídica (CNPJ) do Destinatário do serviço **(escolha)** |
| `DPS/infDPS/IBSCBS/dest/CPF` | 0-1 | TSCPF | maxLength=11, pattern=[0-9]{11} | Número da inscrição no Cadastro de Pessoa Física (CPF) do Destinatário do serviço **(escolha)** |
| `DPS/infDPS/IBSCBS/dest/NIF` | 0-1 | TSNIF | minLength=1, maxLength=40 | Número de Identificação Fiscal fornecido por órgão de administração tributária no exterior **(escolha)** |
| `DPS/infDPS/IBSCBS/dest/cNaoNIF` | 0-1 | TSCodNaoNIF | `0` Não informado na nota de origem<br>`1` Dispensado do NIF<br>`2` Não exigência do NIF<br>→ `Enums\NoNifReason` | Motivo para não informação do NIF: 0 - Não informado na nota de origem; 1 - Dispensado do NIF; 2 - Não exigência do NIF; **(escolha)** |
| `DPS/infDPS/IBSCBS/dest/xNome` | 1-1 | TSDesc150 | minLength=1, maxLength=150 | Nome / Nome Empresarial do do Destinatário do serviço |
| **`DPS/infDPS/IBSCBS/dest/end`** | 0-1 | grupo | | Grupo de informações do endereço do Destinatário do serviço |
| **`DPS/infDPS/IBSCBS/dest/end/endNac`** | 0-1 | grupo | | Grupo de informações específicas de endereço nacional **(escolha)** |
| `DPS/infDPS/IBSCBS/dest/end/endNac/cMun` | 1-1 | TSCodMunIBGE | pattern=[0-9]{7} | Código do município, conforme Tabela do IBGE |
| `DPS/infDPS/IBSCBS/dest/end/endNac/CEP` | 1-1 | TSCEP | pattern=[0-9]{8} | Número do CEP |
| **`DPS/infDPS/IBSCBS/dest/end/endExt`** | 0-1 | grupo | | Grupo de informações específicas de endereço no exterior **(escolha)** |
| `DPS/infDPS/IBSCBS/dest/end/endExt/cPais` | 1-1 | TSCodPaisISO | pattern=[A-Z]{2} | Código do país (Tabela de Países ISO) |
| `DPS/infDPS/IBSCBS/dest/end/endExt/cEndPost` | 1-1 | TSCodigoEndPostal | minLength=1, maxLength=11 | Código alfanumérico do Endereçamento Postal no exterior do prestador do serviço. |
| `DPS/infDPS/IBSCBS/dest/end/endExt/xCidade` | 1-1 | TSCidade | minLength=1, maxLength=60 | Nome da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/IBSCBS/dest/end/endExt/xEstProvReg` | 1-1 | TSEstadoProvRegiao | minLength=1, maxLength=60 | Estado, província ou região da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/IBSCBS/dest/end/xLgr` | 1-1 | TSLogradouro | minLength=1, maxLength=255 | Tipo e nome do logradouro da localização do imóvel |
| `DPS/infDPS/IBSCBS/dest/end/nro` | 1-1 | TSNumeroEndereco | minLength=1, maxLength=60 | Número do imóvel |
| `DPS/infDPS/IBSCBS/dest/end/xCpl` | 0-1 | TSComplementoEndereco | minLength=1, maxLength=156 | Complemento do endereço |
| `DPS/infDPS/IBSCBS/dest/end/xBairro` | 1-1 | TSBairro | minLength=1, maxLength=60 | Bairro |
| `DPS/infDPS/IBSCBS/dest/fone` | 0-1 | TSTelefone | pattern=[0-9]{6,20} | Número do telefone do Destinatário do serviço (Preencher com o Código DDD + número do telefone. Nas operações com exterior é permitido informar o código do país + código da localidade + número do telefone) |
| `DPS/infDPS/IBSCBS/dest/email` | 0-1 | TSEmail | minLength=1, maxLength=80 | E-mail do Destinatário do serviço |
| **`DPS/infDPS/IBSCBS/imovel`** | 0-1 | grupo | | Grupo de informações de operações relacionadas a bens imóveis, exceto obras |
| `DPS/infDPS/IBSCBS/imovel/inscImobFisc` | 0-1 | TSInscImobFisc | minLength=1, maxLength=30 | Inscrição imobiliária fiscal (código fornecido pela Prefeitura Municipal para a identificação da obra ou para fins de recolhimento do IPTU) |
| `DPS/infDPS/IBSCBS/imovel/cCIB` | 0-1 | TSCodCIB | length=8 | Código do Cadastro Imobiliário Brasileiro - CIB **(escolha)** |
| **`DPS/infDPS/IBSCBS/imovel/end`** | 0-1 | grupo | | Grupo de informações do endereço da obra do serviço prestado **(escolha)** |
| `DPS/infDPS/IBSCBS/imovel/end/CEP` | 0-1 | TSCEP | pattern=[0-9]{8} | Número do CEP **(escolha)** |
| **`DPS/infDPS/IBSCBS/imovel/end/endExt`** | 0-1 | grupo | | Grupo de informações específicas de endereço no exterior **(escolha)** |
| `DPS/infDPS/IBSCBS/imovel/end/endExt/cEndPost` | 1-1 | TSCodigoEndPostal | minLength=1, maxLength=11 | Código alfanumérico do Endereçamento Postal no exterior do prestador do serviço. |
| `DPS/infDPS/IBSCBS/imovel/end/endExt/xCidade` | 1-1 | TSCidade | minLength=1, maxLength=60 | Nome da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/IBSCBS/imovel/end/endExt/xEstProvReg` | 1-1 | TSEstadoProvRegiao | minLength=1, maxLength=60 | Estado, província ou região da cidade no exterior do prestador do serviço. |
| `DPS/infDPS/IBSCBS/imovel/end/xLgr` | 1-1 | TSLogradouro | minLength=1, maxLength=255 | Tipo e nome do logradouro da localização do imóvel |
| `DPS/infDPS/IBSCBS/imovel/end/nro` | 1-1 | TSNumeroEndereco | minLength=1, maxLength=60 | Número do imóvel |
| `DPS/infDPS/IBSCBS/imovel/end/xCpl` | 0-1 | TSComplementoEndereco | minLength=1, maxLength=156 | Complemento do endereço |
| `DPS/infDPS/IBSCBS/imovel/end/xBairro` | 1-1 | TSBairro | minLength=1, maxLength=60 | Bairro |
| **`DPS/infDPS/IBSCBS/valores`** | 1-1 | grupo | | Grupo de informações relativas aos valores do serviço prestado para IBS e CBS |
| **`DPS/infDPS/IBSCBS/valores/gReeRepRes`** | 0-1 | grupo | | Grupo de informações relativas a valores incluídos neste documento e recebidos por motivo de estarem relacionadas a operações de terceiros, objeto de reembolso, repasse ou ressarcimento pelo recebedor, já tributados e aqui referenciados |
| **`DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos`** | 1-1000 | grupo | | Grupo relativo aos documentos referenciados nos casos de reembolso, repasse e ressarcimento que serão considerados na base de cálculo do ISSQN, do IBS e da CBS |
| **`DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/dFeNacional`** | 0-1 | grupo | | Grupo de informações de documentos fiscais eletrônicos que se encontram no repositório nacional **(escolha)** |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/dFeNacional/tipoChaveDFe` | 1-1 | TSRTCTipoChaveDFe | `1` NFS-e<br>`2` NF-e<br>`3` CT-e<br>`9` Outro<br>→ `Enums\ReferencedDocumentType` | Documento fiscal a que se refere a chaveDfe que seja um dos documentos do Repositório Nacional |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/dFeNacional/xTipoChaveDFe` | 0-1 | TSDesc255 | minLength=1, maxLength=255 | Descrição da DF-e a que se refere a chaveDfe que seja um dos documentos do Repositório Nacional Deve ser preenchido apenas quando "tipoChaveDFe = 9 (Outro)" |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/dFeNacional/chaveDFe` | 1-1 | TSRTCChaveDFe | minLength=1, maxLength=50 | Chave do Documento Fiscal eletrônico do repositório nacional referenciado para os casos de operações já tributadas |
| **`DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/docFiscalOutro`** | 0-1 | grupo | | Grupo de informações de documento fiscais, eletrônicos ou não, que não se encontram no repositório nacional **(escolha)** |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/docFiscalOutro/cMunDocFiscal` | 1-1 | TSNum7Dig | maxLength=7, pattern=[0-9]{7} | Código do município emissor do documento fiscal que não se encontra no repositório nacional |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/docFiscalOutro/nDocFiscal` | 1-1 | TSDesc255 | minLength=1, maxLength=255 | Número do documento fiscal que não se encontra no repositório nacional |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/docFiscalOutro/xDocFiscal` | 1-1 | TSDesc255 | minLength=1, maxLength=255 | Descrição do documento fiscal |
| **`DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/docOutro`** | 0-1 | grupo | | Grupo de informações de documento não fiscal. **(escolha)** |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/docOutro/nDoc` | 1-1 | TSDesc255 | minLength=1, maxLength=255 | Número do documento não fiscal |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/docOutro/xDoc` | 1-1 | TSDesc255 | minLength=1, maxLength=255 | Descrição do documento não fiscal |
| **`DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/fornec`** | 0-1 | grupo | | Grupo de informações do fornecedor do documento referenciado |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/fornec/CNPJ` | 0-1 | TSCNPJ | maxLength=14, pattern=[0-9]{14} | Número da inscrição no Cadastro Nacional de Pessoa Jurídica (CNPJ) do Fornecedor do serviço **(escolha)** |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/fornec/CPF` | 0-1 | TSCPF | maxLength=11, pattern=[0-9]{11} | Número da inscrição no Cadastro de Pessoa Física (CPF) do Fornecedor do serviço **(escolha)** |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/fornec/NIF` | 0-1 | TSNIF | minLength=1, maxLength=40 | Este elemento só deverá ser preenchido para fornecedores não residentes no Brasil **(escolha)** |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/fornec/cNaoNIF` | 0-1 | TSCodNaoNIF | `0` Não informado na nota de origem<br>`1` Dispensado do NIF<br>`2` Não exigência do NIF<br>→ `Enums\NoNifReason` | Motivo para não informação do NIF: 0 - Não informado na nota de origem; 1 - Dispensado do NIF; 2 - Não exigência do NIF; **(escolha)** |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/fornec/xNome` | 1-1 | TSDesc150 | minLength=1, maxLength=150 | Nome / Razão Social do do Fornecedor do serviço |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/dtEmiDoc` | 1-1 | TSData | pattern=(((20(([02468][048])\|([13579][26]))-02-29))\|(20[0-9][0-9])-((((0[1-9])\|(1[0-2]))-((0[1-9])\|(1\d)\|(2[0-8])))\|((((0[13578])\|(1[02]))-31)\|(((0[1,3-9])\|(1[0-2]))-(29\|30))))) | Data da emissão do documento dedutível Ano, mês e dia (AAAA-MM-DD) |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/dtCompDoc` | 1-1 | TSData | pattern=(((20(([02468][048])\|([13579][26]))-02-29))\|(20[0-9][0-9])-((((0[1-9])\|(1[0-2]))-((0[1-9])\|(1\d)\|(2[0-8])))\|((((0[13578])\|(1[02]))-31)\|(((0[1,3-9])\|(1[0-2]))-(29\|30))))) | Data da competência do documento dedutível Ano, mês e dia (AAAA-MM-DD) |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/tpReeRepRes` | 1-1 | TSRTCTpReeRepRes | `01` Repasse de remuneração por intermediação de imóveis a demais corretores envolvidos na operação<br>`02` Repasse de valores a fornecedor relativo a fornecimento intermediado por agência de turismo<br>`03` Reembolso ou ressarcimento recebido por agência de propaganda e publicidade por valores pagos relativos<br>`04` Reembolso ou ressarcimento recebido por agência de propaganda e publicidade por valores pagos relativos<br>`99` Outros reembolsos ou ressarcimentos recebidos por valores pagos relativos a operações por conta e ordem de terceiro<br>→ `Enums\ReimbursementType` | Tipo de valor incluído neste documento, recebido por motivo de estarem relacionadas a operações de terceiros, objeto de reembolso, repasse ou ressarcimento pelo recebedor, já tributados e aqui referenciados |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/xTpReeRepRes` | 0-1 | TSDesc150 | minLength=1, maxLength=150 | Descrição do reembolso ou ressarcimento quando a opção é "99 – Outros reembolsos ou ressarcimentos recebidos por valores pagos relativos a operações por conta e ordem de terceiro" |
| `DPS/infDPS/IBSCBS/valores/gReeRepRes/documentos/vlrReeRepRes` | 1-1 | TSDec15V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,14}(\.[0-9]{2})? | Valor monetário (total ou parcial, conforme documento informado) utilizado para não inclusão na base de cálculo do ISS e do IBS e da CBS da NFS-e que está sendo emitida (R$) |
| **`DPS/infDPS/IBSCBS/valores/trib`** | 1-1 | grupo | | Grupo de informações relacionados aos tributos IBS e CBS |
| **`DPS/infDPS/IBSCBS/valores/trib/gIBSCBS`** | 1-1 | grupo | | Grupo de informações relacionadas ao IBS e à CBS |
| `DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/CST` | 1-1 | TSRTCCodSitTrib | pattern=[0-9]{3} | Código de Situação Tributária do IBS e da CBS |
| `DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/cClassTrib` | 1-1 | TSRTCCodClassTrib | pattern=[0-9]{6} | Código de Classificação Tributária do IBS e da CBS |
| `DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/cCredPres` | 0-1 | TSRTCCodCredPres | pattern=[0-9]{2} | Código e Classificação do Crédito Presumido: IBS e CBS |
| **`DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/gTribRegular`** | 0-1 | grupo | | Grupo de informações da Tributação Regular |
| `DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/gTribRegular/CSTReg` | 1-1 | TSRTCCodSitTrib | pattern=[0-9]{3} | Código de Situação Tributária do IBS e da CBS de tributação regular |
| `DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/gTribRegular/cClassTribReg` | 1-1 | TSRTCCodClassTrib | pattern=[0-9]{6} | Código da Classificação Tributária do IBS e da CBS de tributação regular |
| **`DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/gDif`** | 0-1 | grupo | | Grupo de informações relacionadas ao diferimento para IBS e CBS |
| `DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/gDif/pDifUF` | 1-1 | TSDec3V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,2}(\.[0-9]{2})? | Percentual de diferimento para o IBS estadual |
| `DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/gDif/pDifMun` | 1-1 | TSDec3V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,2}(\.[0-9]{2})? | Percentual de diferimento para o IBS municipal |
| `DPS/infDPS/IBSCBS/valores/trib/gIBSCBS/gDif/pDifCBS` | 1-1 | TSDec3V2 | pattern=0\|0\.[0-9]{2}\|[1-9]{1}[0-9]{0,2}(\.[0-9]{2})? | Percentual de diferimento para a CBS |
| `DPS/Signature` | 0-1 | XMLDSig | gerada pelo pacote | Assinatura digital |

## Pedido de registro de evento (cancelamento, substituição etc.)

| Caminho | Ocor. | Tipo | Opções / formato | Descrição |
|---|---|---|---|---|
| `pedRegEvento/@versao` | 1-1 | TVerNFSe | maxLength=4, pattern=1\.00\|1\.01 |  |
| **`pedRegEvento/infPedReg`** | 1-1 | grupo | |  |
| `pedRegEvento/infPedReg/@Id` | 1-1 | TSIdPedRegEvt | maxLength=59, pattern=PRE[0-9]{56} |  |
| `pedRegEvento/infPedReg/tpAmb` | 1-1 | TSTipoAmbiente | `1` Produção<br>`2` Homologação<br>→ `Enums\Environment` | Tipo de ambiente: 1 - Produção; 2 - Homologação; |
| `pedRegEvento/infPedReg/verAplic` | 1-1 | TSVerAplic | minLength=1, maxLength=20 | Versão do aplicativo que gerou o pedido de registro de evento |
| `pedRegEvento/infPedReg/dhEvento` | 1-1 | TSDateTimeUTC | pattern=(((20(([02468][048])\|([13579][26]))-02-29))\|(20[0-9][0-9])-((((0[1-9])\|(1[0-2]))-((0[1-9])\|(1\d)\|(2[0-8])))\|((((0[13578])\|(1[02]))-31)\|(((0[1,3-9])\|(1[0-2]))-(29\|30)))))T(20\|21\|22\|23\|[0-1]\d):[0-5]\d:[0-5]\d([\-,\+](0[0-9]\|10\|11):00\|([\+](12):00)) | Data e hora do evento no formato AAAA-MM-DDThh:mm:ssTZD (UTC - Universal Coordinated Time, onde TZD pode ser -02:00 (Fernando de Noronha), -03:00 (Brasília) ou -04:00 (Manaus), no horário de verão serão -01:00, -02:00 e -03:00. Ex.: 2010-08-19T13:00:15-03:00. |
| `pedRegEvento/infPedReg/CNPJAutor` | 0-1 | TSCNPJ | maxLength=14, pattern=[0-9]{14} | Número de inscrição federal (CNPJ) do autor do evento. CNPJ do autor do evento (parte interessada ou pessoa que figure na NFS-e. O autor do evento não é o procurador) **(escolha)** |
| `pedRegEvento/infPedReg/CPFAutor` | 0-1 | TSCPF | maxLength=11, pattern=[0-9]{11} | Número de inscrição federal (CPF) do autor do evento. CPF do autor do evento (parte interessada ou pessoa que figure na NFS-e como prestador, tomador, intermediário. O autor do evento poderá ser o procurador) **(escolha)** |
| `pedRegEvento/infPedReg/chNFSe` | 1-1 | TSChaveNFSe | maxLength=50, pattern=[0-9]{50} | Identificador da NFS-e à qual o evento será vinculado |
| **`pedRegEvento/infPedReg/e101101`** | 0-1 | grupo | | Evento de cancelamento **(escolha)** |
| `pedRegEvento/infPedReg/e101101/xDesc` | 1-1 | TE101101_xDesc | `Cancelamento de NFS-e` | Descrição do Evento: Descrição do evento: "Cancelamento de NFS-e". |
| `pedRegEvento/infPedReg/e101101/cMotivo` | 1-1 | TSCodJustCanc | `1` Erro na Emissão<br>`2` Serviço não Prestado<br>`9` Outros<br>→ `Enums\CancellationReason` | Código de justificativa de cancelamento: 1 - Erro na Emissão; 2 - Serviço não Prestado; 9 - Outros; |
| `pedRegEvento/infPedReg/e101101/xMotivo` | 1-1 | TSMotivo | minLength=15, maxLength=255 | Descrição para explicitar o motivo indicado neste evento |
| **`pedRegEvento/infPedReg/e105102`** | 0-1 | grupo | | Evento de cancelamento por substituição **(escolha)** |
| `pedRegEvento/infPedReg/e105102/xDesc` | 1-1 | TE105102_xDesc | `Cancelamento de NFS-e por Substituição` | Descrição do Evento: Descrição do evento: "Cancelamento de NFS-e por Substituição". |
| `pedRegEvento/infPedReg/e105102/cMotivo` | 1-1 | TSCodJustSubst | `01` Desenquadramento de NFS-e do Simples Nacional<br>`02` Enquadramento de NFS-e no Simples Nacional<br>`03` Inclusão Retroativa de Imunidade/Isenção para NFS-e<br>`04` Exclusão Retroativa de Imunidade/Isenção para NFS-e<br>`05` Rejeição de NFS-e pelo tomador ou pelo intermediário se responsável pelo recolhimento do tributo<br>`99` Outros<br>→ `Enums\ReplacementReason` | Código de justificativa de cancelamento substituição: 01 - Desenquadramento de NFS-e do Simples Nacional; 02 - Enquadramento de NFS-e no Simples Nacional; 03 - Inclusão Retroativa de Imunidade/Isenção para NFS-e; 04 - Exclusão Retroativa de Imunidade/Isenção para NFS-e; 05 - Rejeição de NFS-e pelo tomador ou pelo intermediário se responsável pelo recolhimento do tributo; 99 - Outros; Obtido do campo da DPS "DPS/infDPS/subst/cMotivo" |
| `pedRegEvento/infPedReg/e105102/xMotivo` | 0-1 | TSMotivo | minLength=15, maxLength=255 | Descrição para explicitar o motivo indicado neste evento. Obtido do campo da DPS "DPS/infDPS/subst/xMotivo". |
| `pedRegEvento/infPedReg/e105102/chSubstituta` | 1-1 | TSChaveNFSe | maxLength=50, pattern=[0-9]{50} | Chave de Acesso da NFS-e substituta |
| **`pedRegEvento/infPedReg/e101103`** | 0-1 | grupo | | Solicitação de Análise Fiscal para Cancelamento de NFS-e **(escolha)** |
| `pedRegEvento/infPedReg/e101103/xDesc` | 1-1 | TE101103_xDesc | `Solicitação de Análise Fiscal para Cancelamento de NFS-e` | Descrição do evento: "Solicitação de Análise Fiscal para Cancelamento de NFS-e" |
| `pedRegEvento/infPedReg/e101103/cMotivo` | 1-1 | TSCodJustAnaliseFiscalCanc | `1` Erro na Emissão<br>`2` Serviço não Prestado<br>`9` | Código do motivo da solicitação de análise fiscal para cancelamento de NFS-e: 1 - Erro na Emissão; 2 - Serviço não Prestado; 9 - Outros; |
| `pedRegEvento/infPedReg/e101103/xMotivo` | 1-1 | TSMotivo | minLength=15, maxLength=255 | Descrição para explicitar o motivo indicado neste evento |
| **`pedRegEvento/infPedReg/e105104`** | 0-1 | grupo | | Cancelamento de NFS-e Deferido por Análise Fiscal **(escolha)** |
| `pedRegEvento/infPedReg/e105104/xDesc` | 1-1 | TE105104_xDesc | `Cancelamento de NFS-e Deferido por Análise Fiscal` | Descrição do evento: "Cancelamento de NFS-e Deferido por Análise Fiscal" |
| `pedRegEvento/infPedReg/e105104/CPFAgTrib` | 1-1 | TSCPF | maxLength=11, pattern=[0-9]{11} | CPF do agente da administração tributária municipal que efetuou o deferimento da solicitação de análise fiscal para cancelamento de NFS-e. |
| `pedRegEvento/infPedReg/e105104/nProcAdm` | 0-1 | TSNumProcAdmAnaliseFiscalCanc | minLength=1, maxLength=30, pattern=[0-9]{1,30} | Número do processo administrativo municipal vinculado à solicitação de análise fiscal para cancelamento de NFS-e. |
| `pedRegEvento/infPedReg/e105104/cMotivo` | 1-1 | TSCodJustAnaliseFiscalCancDef | `1` Cancelamento Extemporâneo Deferido | Resposta da solicitação de análise fiscal para cancelamento de NFS-e: 1 - Cancelamento de NFS-e Deferido. |
| `pedRegEvento/infPedReg/e105104/xMotivo` | 1-1 | TSMotivo | minLength=15, maxLength=255 | Descrição para explicitar o motivo indicado neste evento |
| **`pedRegEvento/infPedReg/e105105`** | 0-1 | grupo | | Cancelamento de NFS-e Indeferido por Análise Fiscal **(escolha)** |
| `pedRegEvento/infPedReg/e105105/xDesc` | 1-1 | TE105105_xDesc | `Cancelamento de NFS-e Indeferido por Análise Fiscal` | Descrição do evento: "Cancelamento de NFS-e Indeferido por Análise Fiscal". |
| `pedRegEvento/infPedReg/e105105/CPFAgTrib` | 1-1 | TSCPF | maxLength=11, pattern=[0-9]{11} | CPF do agente da administração tributária municipal que efetuou o indeferimento da solicitação de análise fiscal para cancelamento de NFS-e. |
| `pedRegEvento/infPedReg/e105105/nProcAdm` | 0-1 | TSNumProcAdmAnaliseFiscalCanc | minLength=1, maxLength=30, pattern=[0-9]{1,30} | Número do processo administrativo municipal vinculado à solicitação de análise fiscal para cancelamento de NFS-e. |
| `pedRegEvento/infPedReg/e105105/cMotivo` | 1-1 | TSCodJustAnaliseFiscalCancIndef | `1` Cancelamento Extemporâneo Indeferido<br>`2` Cancelamento Extemporâneo Indeferido Sem Análise de Mérito | Resposta da solicitação de análise fiscal para cancelamento de NFS-e: 1 - Cancelamento de NFS-e Indeferido; 2 - Cancelamento de NFS-e Indeferido Sem Análise de Mérito. |
| `pedRegEvento/infPedReg/e105105/xMotivo` | 1-1 | TSMotivo | minLength=15, maxLength=255 | Descrição para explicitar o motivo indicado neste evento |
| **`pedRegEvento/infPedReg/e202201`** | 0-1 | grupo | | Confirmação do Prestador **(escolha)** |
| `pedRegEvento/infPedReg/e202201/xDesc` | 1-1 | TE202201_xDesc | `Manifestação de NFS-e - Confirmação do Prestador` | Descrição do evento: "Manifestação de NFS-e - Confirmação do Prestador". |
| **`pedRegEvento/infPedReg/e203202`** | 0-1 | grupo | | Confirmação do Tomador **(escolha)** |
| `pedRegEvento/infPedReg/e203202/xDesc` | 1-1 | TE203202_xDesc | `Manifestação de NFS-e - Confirmação do Tomador` | Descrição do evento: "Manifestação de NFS-e - Confirmação do Tomador". |
| **`pedRegEvento/infPedReg/e204203`** | 0-1 | grupo | | Confirmação do Intermediário **(escolha)** |
| `pedRegEvento/infPedReg/e204203/xDesc` | 1-1 | TE204203_xDesc | `Manifestação de NFS-e - Confirmação do Intermediário` | Descrição do evento: "Manifestação de NFS-e - Confirmação do Intermediário". |
| **`pedRegEvento/infPedReg/e205204`** | 0-1 | grupo | | Confirmação Tácita **(escolha)** |
| `pedRegEvento/infPedReg/e205204/xDesc` | 1-1 | TE205204_xDesc | `Manifestação de NFS-e - Confirmação Tácita` | Descrição do evento: "Manifestação de NFS-e - Confirmação Tácita". |
| **`pedRegEvento/infPedReg/e202205`** | 0-1 | grupo | | Rejeição do Prestador **(escolha)** |
| `pedRegEvento/infPedReg/e202205/xDesc` | 1-1 | TE202205_xDesc | `Manifestação de NFS-e - Rejeição do Prestador` | Descrição do evento: "Manifestação de NFS-e - Rejeição do Prestador". |
| `pedRegEvento/infPedReg/e202205/cMotivo` | 1-1 | TSCodMotivoRejeicao | `1` NFS-e em duplicidade<br>`2` NFS-e já emitida pelo tomador<br>`3` Não ocorrência do fato gerador<br>`4` Erro quanto a responsabilidade tributária<br>`5` Erro quanto ao valor do serviço, valor das deduções ou serviço prestado ou data do fato gerador<br>`9` Outros | Motivo da Rejeição da NFS-e: 1 - NFS-e em duplicidade; 2 - NFS-e já emitida pelo tomador; 3 - Não ocorrência do fato gerador; 4 - Erro quanto a responsabilidade tributária; 5 - Erro quanto ao valor do serviço, valor das deduções ou serviço prestado ou data do fato gerador; 9 - Outros; |
| `pedRegEvento/infPedReg/e202205/xMotivo` | 0-1 | TSMotivo | minLength=15, maxLength=255 | Descrição para explicitar o motivo indicado neste evento |
| **`pedRegEvento/infPedReg/e203206`** | 0-1 | grupo | | Rejeição do Tomador **(escolha)** |
| `pedRegEvento/infPedReg/e203206/xDesc` | 1-1 | TE203206_xDesc | `Manifestação de NFS-e - Rejeição do Tomador` | Descrição do evento: "Manifestação de NFS-e - Rejeição do Tomador". |
| `pedRegEvento/infPedReg/e203206/cMotivo` | 1-1 | TSCodMotivoRejeicao | `1` NFS-e em duplicidade<br>`2` NFS-e já emitida pelo tomador<br>`3` Não ocorrência do fato gerador<br>`4` Erro quanto a responsabilidade tributária<br>`5` Erro quanto ao valor do serviço, valor das deduções ou serviço prestado ou data do fato gerador<br>`9` Outros | Motivo da Rejeição da NFS-e: 1 - NFS-e em duplicidade; 2 - NFS-e já emitida pelo tomador; 3 - Não ocorrência do fato gerador; 4 - Erro quanto a responsabilidade tributária; 5 - Erro quanto ao valor do serviço, valor das deduções ou serviço prestado ou data do fato gerador; 9 - Outros; |
| `pedRegEvento/infPedReg/e203206/xMotivo` | 0-1 | TSMotivo | minLength=15, maxLength=255 | Descrição para explicitar o motivo indicado neste evento |
| **`pedRegEvento/infPedReg/e204207`** | 0-1 | grupo | | Rejeição do Intermediário **(escolha)** |
| `pedRegEvento/infPedReg/e204207/xDesc` | 1-1 | TE204207_xDesc | `Manifestação de NFS-e - Rejeição do Intermediário` | Descrição do evento: "Manifestação de NFS-e - Rejeição do Intermediário". |
| `pedRegEvento/infPedReg/e204207/cMotivo` | 1-1 | TSCodMotivoRejeicao | `1` NFS-e em duplicidade<br>`2` NFS-e já emitida pelo tomador<br>`3` Não ocorrência do fato gerador<br>`4` Erro quanto a responsabilidade tributária<br>`5` Erro quanto ao valor do serviço, valor das deduções ou serviço prestado ou data do fato gerador<br>`9` Outros | Motivo da Rejeição da NFS-e: 1 - NFS-e em duplicidade; 2 - NFS-e já emitida pelo tomador; 3 - Não ocorrência do fato gerador; 4 - Erro quanto a responsabilidade tributária; 5 - Erro quanto ao valor do serviço, valor das deduções ou serviço prestado ou data do fato gerador; 9 - Outros; |
| `pedRegEvento/infPedReg/e204207/xMotivo` | 0-1 | TSMotivo | minLength=15, maxLength=255 | Descrição para explicitar o motivo indicado neste evento |
| **`pedRegEvento/infPedReg/e205208`** | 0-1 | grupo | | Anulação da Rejeição **(escolha)** |
| `pedRegEvento/infPedReg/e205208/xDesc` | 1-1 | TE205208_xDesc | `Manifestação de NFS-e - Anulação da Rejeição` | Descrição do evento: "Manifestação de NFS-e - Anulação da Rejeição". |
| `pedRegEvento/infPedReg/e205208/CPFAgTrib` | 1-1 | TSCPF | maxLength=11, pattern=[0-9]{11} | CPF do agente da administração tributária municipal que efetuou o anulação da manifestação de rejeição da NFS-e |
| `pedRegEvento/infPedReg/e205208/idEvManifRej` | 1-1 | TSIdNumEvento | pattern=[0-9]{59} | Referência ao "id" do Evento de Manifestação de NFS-e - Rejeição, que originou o presente evento de anulação |
| `pedRegEvento/infPedReg/e205208/xMotivo` | 1-1 | TSMotivo | minLength=15, maxLength=255 | Descrição para explicitar o motivo indicado neste evento |
| **`pedRegEvento/infPedReg/e305101`** | 0-1 | grupo | | Cancelamento de NFS-e por Ofício **(escolha)** |
| `pedRegEvento/infPedReg/e305101/xDesc` | 1-1 | TE305101_xDesc | `Cancelamento de NFS-e por Ofício` | Descrição do evento: "Cancelamento de NFS-e por Ofício" |
| `pedRegEvento/infPedReg/e305101/CPFAgTrib` | 1-1 | TSCPF | maxLength=11, pattern=[0-9]{11} | CPF do agente da administração tributária municipal que efetuou o cancelamento por ofício de NFS-e |
| `pedRegEvento/infPedReg/e305101/nProcAdm` | 1-1 | TSNumProcAdmAnaliseFiscalCanc | minLength=1, maxLength=30, pattern=[0-9]{1,30} | Número do processo administrativo municipal vinculado ao cancelamento de NFS-e por ofício |
| `pedRegEvento/infPedReg/e305101/xProcAdm` | 1-1 | TSMotivo | minLength=15, maxLength=255 | Descrição para explicitar o motivo do processo administrativo municipal indicado neste evento |
| **`pedRegEvento/infPedReg/e305102`** | 0-1 | grupo | | Bloqueio de NFS-e por Ofício **(escolha)** |
| `pedRegEvento/infPedReg/e305102/xDesc` | 1-1 | TE305102_xDesc | `Bloqueio de NFS-e por Ofício` | Descrição do evento: "Bloqueio de NFS-e por Ofício". |
| `pedRegEvento/infPedReg/e305102/CPFAgTrib` | 1-1 | TSCPF | maxLength=11, pattern=[0-9]{11} | CPF do agente da administração tributária municipal que efetuou o bloqueio de NFS-e por ofício |
| `pedRegEvento/infPedReg/e305102/codEvento` | 1-1 | TSCodigoEventoNFSe | `e101101`<br>`e105102`<br>`e105104`<br>`e105105`<br>`e305101`<br>→ `Enums\EventCode` | Eventos que podem ser escolhidos pelo município emissor para serem rejeitados após emissão e vinculação do evento de bloqueio por ofício em uma NFS-e: e101101 - Cancelamento de NFS-e; e105102 - Cancelamento de NFS-e por Substituição; e105104 - Cancelamento de NFS-e Deferido por Análise Fiscal; e105105 - Cancelamento de NFS-e Indeferido por Análise Fiscal; e305101 - Cancelamento de NFS-e por Ofício; |
| `pedRegEvento/infPedReg/e305102/xMotivo` | 1-1 | TSMotivo | minLength=15, maxLength=255 | Descrição para explicitar o motivo indicado neste evento |
| **`pedRegEvento/infPedReg/e305103`** | 0-1 | grupo | | Desbloqueio de NFS-e por Ofício **(escolha)** |
| `pedRegEvento/infPedReg/e305103/xDesc` | 1-1 | TE305103_xDesc | `Desbloqueio de NFS-e por Ofício` | Descrição do evento: "Desbloqueio de NFS-e por Ofício". |
| `pedRegEvento/infPedReg/e305103/CPFAgTrib` | 1-1 | TSCPF | maxLength=11, pattern=[0-9]{11} | CPF do agente da administração tributária municipal que efetuou o desbloqueio de NFS-e por ofício |
| `pedRegEvento/infPedReg/e305103/idBloqOfic` | 1-1 | TSIdNumEvento | pattern=[0-9]{59} | Referência ao "id" do "Bloqueio de ofício" que originou o presente evento de desbloqueio |
| `pedRegEvento/Signature` | 0-1 | XMLDSig | gerada pelo pacote | Assinatura digital |

