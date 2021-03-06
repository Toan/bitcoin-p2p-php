<?php

namespace BitWasp\Bitcoin\Networking\Messages;

use BitWasp\Bitcoin\Block\BlockInterface;
use BitWasp\Bitcoin\Block\FilteredBlock;
use BitWasp\Bitcoin\Bloom\BloomFilter;
use BitWasp\Bitcoin\Chain\BlockLocator;
use BitWasp\Bitcoin\Crypto\Random\Random;
use BitWasp\Bitcoin\Network\NetworkInterface;
use BitWasp\Bitcoin\Networking\NetworkMessage;
use BitWasp\Bitcoin\Networking\Structure\AlertDetail;
use BitWasp\Bitcoin\Networking\Structure\Inventory;
use BitWasp\Bitcoin\Networking\Structure\NetworkAddressInterface;
use BitWasp\Bitcoin\Networking\Serializer\NetworkMessageSerializer;
use BitWasp\Bitcoin\Networking\Structure\NetworkAddressTimestamp;
use BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface;
use BitWasp\Bitcoin\Transaction\TransactionInterface;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;
use BitWasp\Buffertools\Parser;

class Factory
{
    /**
     * @var NetworkInterface
     */
    private $network;

    /**
     * @var Random
     */
    private $random;

    /**
     * @param NetworkInterface $network
     * @param Random $random
     */
    public function __construct(NetworkInterface $network, Random $random)
    {
        $this->network = $network;
        $this->random = $random;
        $this->serializer = new NetworkMessageSerializer($this->network);
    }

    /**
     * @param int $version
     * @param int $services
     * @param int $timestamp
     * @param NetworkAddressInterface $addrRecv
     * @param NetworkAddressInterface $addrFrom
     * @param BufferInterface $userAgent
     * @param int $startHeight
     * @param bool $relay
     * @return Version
     */
    public function version(
        $version,
        $services,
        $timestamp,
        NetworkAddressInterface $addrRecv,
        NetworkAddressInterface $addrFrom,
        BufferInterface $userAgent,
        $startHeight,
        $relay
    ) {
        return new Version(
            $version,
            $services,
            $timestamp,
            $addrRecv,
            $addrFrom,
            $this->random->bytes(8)->getInt(),
            $userAgent,
            $startHeight,
            $relay
        );
    }

    /**
     * @return VerAck
     */
    public function verack()
    {
        return new VerAck();
    }

    /**
     * @return SendHeaders
     */
    public function sendheaders()
    {
        return new SendHeaders();
    }

    /**
     * @param NetworkAddressTimestamp[] $addrs
     * @return Addr
     */
    public function addr(array $addrs)
    {
        return new Addr($addrs);
    }

    /**
     * @param Inventory[] $vectors
     * @return Inv
     */
    public function inv(array $vectors)
    {
        return new Inv($vectors);
    }

    /**
     * @param Inventory[] $vectors
     * @return GetData
     */
    public function getdata(array $vectors)
    {
        return new GetData($vectors);
    }

    /**
     * @param Inventory[] $vectors
     * @return NotFound
     */
    public function notfound(array $vectors)
    {
        return new NotFound($vectors);
    }

    /**
     * @param $version
     * @param BlockLocator $blockLocator
     * @return GetBlocks
     */
    public function getblocks($version, BlockLocator $blockLocator)
    {
        return new GetBlocks($version, $blockLocator);
    }

    /**
     * @param $version
     * @param BlockLocator $blockLocator
     * @return GetHeaders
     */
    public function getheaders($version, BlockLocator $blockLocator)
    {
        return new GetHeaders($version, $blockLocator);
    }

    /**
     * @param TransactionInterface $tx
     * @return Tx
     */
    public function tx(TransactionInterface $tx)
    {
        return new Tx($tx);
    }

    /**
     * @param BlockInterface $block
     * @return Block
     */
    public function block(BlockInterface $block)
    {
        return new Block($block);
    }

    /**
     * @param \BitWasp\Bitcoin\Block\BlockHeaderInterface[] $headers
     * @return Headers
     */
    public function headers(array $headers)
    {
        return new Headers($headers);
    }

    /**
     * @return GetAddr
     */
    public function getaddr()
    {
        return new GetAddr();
    }

    /**
     * @return MemPool
     */
    public function mempool()
    {
        return new MemPool();
    }

    /**
     * @param int $feeRate
     * @return FeeFilter
     */
    public function feefilter($feeRate)
    {
        return new FeeFilter($feeRate);
    }

    /**
     * @param BufferInterface $data
     * @return FilterAdd
     */
    public function filteradd(BufferInterface $data)
    {
        return new FilterAdd($data);
    }

    /**
     * @param BloomFilter $filter
     * @return FilterLoad
     */
    public function filterload(BloomFilter $filter)
    {
        return new FilterLoad($filter);
    }

    /**
     * @return FilterClear
     */
    public function filterclear()
    {
        return new FilterClear();
    }

    /**
     * @param FilteredBlock $filtered
     * @return MerkleBlock
     */
    public function merkleblock(FilteredBlock $filtered)
    {
        return new MerkleBlock($filtered);
    }
    /**
     * @return Ping
     * @throws \BitWasp\Bitcoin\Exceptions\RandomBytesFailure
     */
    public function ping()
    {
        return new Ping($this->random->bytes(8)->getInt());
    }

    /**
     * @param Ping $ping
     * @return Pong
     */
    public function pong(Ping $ping)
    {
        return new Pong($ping->getNonce());
    }

    /**
     * @param BufferInterface $message
     * @param int $code
     * @param BufferInterface $reason
     * @param BufferInterface|null $data
     * @return Reject
     */
    public function reject(
        BufferInterface $message,
        $code,
        BufferInterface $reason,
        BufferInterface $data = null
    ) {
        if (null === $data) {
            $data = new Buffer();
        }

        return new Reject(
            $message,
            $code,
            $reason,
            $data
        );
    }

    /**
     * @param AlertDetail $detail
     * @param SignatureInterface $sig
     * @return Alert
     */
    public function alert(AlertDetail $detail, SignatureInterface $sig)
    {
        return new Alert(
            $detail,
            $sig
        );
    }

    /**
     * @param Parser $parser
     * @return NetworkMessage
     */
    public function parse(Parser $parser)
    {
        return $this->serializer->fromParser($parser);
    }

    /**
     * @return NetworkMessageSerializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @return NetworkInterface
     */
    public function getNetwork()
    {
        return $this->network;
    }
}
