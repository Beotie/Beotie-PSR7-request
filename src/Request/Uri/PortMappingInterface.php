<?php
declare(strict_types=1);
/**
 * This file is part of beotie/core_bundle
 *
 * As each files provides by the CSCFA, this file is licensed
 * under the MIT license.
 *
 * PHP version 7.1
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
namespace Beotie\PSR7\Request\Uri;

/**
 * Port mappin interface
 *
 * This interface is used to represent the UTP/UDP common port mapping
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
interface PortMappingInterface
{
    /**
     * ACAP
     *
     * This constant define the standard port for ACAP scheme
     *
     * @var integer
     */
    const ACAP = 674;

    /**
     * AFP
     *
     * This constant define the standard port for AFP scheme
     *
     * @var integer
     */
    const AFP = 548;

    /**
     * DICT
     *
     * This constant define the standard port for DICT scheme
     *
     * @var integer
     */
    const DICT = 2628;

    /**
     * DNS
     *
     * This constant define the standard port for DNS scheme
     *
     * @var integer
     */
    const DNS = 53;

    /**
     * FILE
     *
     * This constant define the standard port for FILE scheme
     *
     * @var null
     */
    const FILE = null;

    /**
     * FTP
     *
     * This constant define the standard port for FTP scheme
     *
     * @var integer
     */
    const FTP = 21;

    /**
     * GIT
     *
     * This constant define the standard port for GIT scheme
     *
     * @var integer
     */
    const GIT = 9418;

    /**
     * GOPHER
     *
     * This constant define the standard port for GOPHER scheme
     *
     * @var integer
     */
    const GOPHER = 70;

    /**
     * HTTP
     *
     * This constant define the standard port for HTTP scheme
     *
     * @var integer
     */
    const HTTP = 80;

    /**
     * HTTPS
     *
     * This constant define the standard port for HTTPS scheme
     *
     * @var integer
     */
    const HTTPS = 443;

    /**
     * IMAP
     *
     * This constant define the standard port for IMAP scheme
     *
     * @var integer
     */
    const IMAP = 143;

    /**
     * IPP
     *
     * This constant define the standard port for IPP scheme
     *
     * @var integer
     */
    const IPP = 631;

    /**
     * IPPS
     *
     * This constant define the standard port for IPPS scheme
     *
     * @var integer
     */
    const IPPS = 631;

    /**
     * IRC
     *
     * This constant define the standard port for IRC scheme
     *
     * @var integer
     */
    const IRC = 194;

    /**
     * IRCS
     *
     * This constant define the standard port for IRCS scheme
     *
     * @var integer
     */
    const IRCS = 6697;

    /**
     * LDAP
     *
     * This constant define the standard port for LDAP scheme
     *
     * @var integer
     */
    const LDAP = 389;

    /**
     * LDAPS
     *
     * This constant define the standard port for LDAPS scheme
     *
     * @var integer
     */
    const LDAPS = 636;

    /**
     * MMS
     *
     * This constant define the standard port for MMS scheme
     *
     * @var integer
     */
    const MMS = 1755;

    /**
     * MSRP
     *
     * This constant define the standard port for MSRP scheme
     *
     * @var integer
     */
    const MSRP = 2855;

    /**
     * MSRPS
     *
     * This constant define the standard port for MSRPS scheme
     *
     * @var null
     */
    const MSRPS = null;

    /**
     * MTQP
     *
     * This constant define the standard port for MTQP scheme
     *
     * @var integer
     */
    const MTQP = 1038;

    /**
     * NFS
     *
     * This constant define the standard port for NFS scheme
     *
     * @var integer
     */
    const NFS = 111;

    /**
     * NNTP
     *
     * This constant define the standard port for NNTP scheme
     *
     * @var integer
     */
    const NNTP = 119;

    /**
     * NNTPS
     *
     * This constant define the standard port for NNTPS scheme
     *
     * @var integer
     */
    const NNTPS = 563;

    /**
     * POP
     *
     * This constant define the standard port for POP scheme
     *
     * @var integer
     */
    const POP = 110;

    /**
     * PROSPERO
     *
     * This constant define the standard port for PROSPERO scheme
     *
     * @var integer
     */
    const PROSPERO = 1525;

    /**
     * REDIS
     *
     * This constant define the standard port for REDIS scheme
     *
     * @var integer
     */
    const REDIS = 6379;

    /**
     * RSYNC
     *
     * This constant define the standard port for RSYNC scheme
     *
     * @var integer
     */
    const RSYNC = 873;

    /**
     * RTSP
     *
     * This constant define the standard port for RTSP scheme
     *
     * @var integer
     */
    const RTSP = 554;

    /**
     * RTSPS
     *
     * This constant define the standard port for RTSPS scheme
     *
     * @var integer
     */
    const RTSPS = 322;

    /**
     * RTSPU
     *
     * This constant define the standard port for RTSPU scheme
     *
     * @var integer
     */
    const RTSPU = 5005;

    /**
     * SFTP
     *
     * This constant define the standard port for SFTP scheme
     *
     * @var integer
     */
    const SFTP = 22;

    /**
     * SMB
     *
     * This constant define the standard port for SMB scheme
     *
     * @var integer
     */
    const SMB = 445;

    /**
     * SNMP
     *
     * This constant define the standard port for SNMP scheme
     *
     * @var integer
     */
    const SNMP = 161;

    /**
     * SSH
     *
     * This constant define the standard port for SSH scheme
     *
     * @var integer
     */
    const SSH = 22;

    /**
     * STEAM
     *
     * This constant define the standard port for STEAM scheme
     *
     * @var null
     */
    const STEAM = null;

    /**
     * SVN
     *
     * This constant define the standard port for SVN scheme
     *
     * @var integer
     */
    const SVN = 3690;

    /**
     * TELNET
     *
     * This constant define the standard port for TELNET scheme
     *
     * @var integer
     */
    const TELNET = 23;

    /**
     * VENTRILO
     *
     * This constant define the standard port for VENTRILO scheme
     *
     * @var integer
     */
    const VENTRILO = 3784;

    /**
     * VNC
     *
     * This constant define the standard port for VNC scheme
     *
     * @var integer
     */
    const VNC = 5900;

    /**
     * WAIS
     *
     * This constant define the standard port for WAIS scheme
     *
     * @var integer
     */
    const WAIS = 210;

    /**
     * WS
     *
     * This constant define the standard port for WS scheme
     *
     * @var integer
     */
    const WS = 80;

    /**
     * WSS
     *
     * This constant define the standard port for WSS scheme
     *
     * @var integer
     */
    const WSS = 443;

    /**
     * XMPP
     *
     * This constant define the standard port for XMPP scheme
     *
     * @var null
     */
    const XMPP = null;

    /**
     * MAPPING
     *
     * This constant define the standard port for existant scheme
     *
     * @var array
     */
    const MAPPING = [
        'acap' => self::ACAP,
        'afp' => self::AFP,
        'dict' => self::DICT,
        'dns' => self::DNS,
        'file' => self::FILE,
        'ftp' => self::FTP,
        'git' => self::GIT,
        'gopher' => self::GOPHER,
        'http' => self::HTTP,
        'https' => self::HTTPS,
        'imap' => self::IMAP,
        'ipp' => self::IPP,
        'ipps' => self::IPPS,
        'irc' => self::IRC,
        'ircs' => self::IRCS,
        'ldap' => self::LDAP,
        'ldaps' => self::LDAPS,
        'mms' => self::MMS,
        'msrp' => self::MSRP,
        'msrps' => self::MSRPS,
        'mtqp' => self::MTQP,
        'nfs' => self::NFS,
        'nntp' => self::NNTP,
        'nntps' => self::NNTPS,
        'pop' => self::POP,
        'prospero' => self::PROSPERO,
        'redis' => self::REDIS,
        'rsync' => self::RSYNC,
        'rtsp' => self::RTSP,
        'rtsps' => self::RTSPS,
        'rtspu' => self::RTSPU,
        'sftp' => self::SFTP,
        'smb' => self::SMB,
        'snmp' => self::SNMP,
        'ssh' => self::SSH,
        'steam' => self::STEAM,
        'svn' => self::SVN,
        'telnet' => self::TELNET,
        'ventrilo' => self::VENTRILO,
        'vnc' => self::VNC,
        'wais' => self::WAIS,
        'ws' => self::WS,
        'wss' => self::WSS,
        'xmpp' => self::XMPP,
    ];
}
