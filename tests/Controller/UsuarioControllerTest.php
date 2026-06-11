<?php

namespace App\Tests\Controller;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UsuarioControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;

    /** @var EntityRepository<Usuario> */
    private EntityRepository $usuarioRepository;
    private string $path = '/usuario/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->usuarioRepository = $this->manager->getRepository(Usuario::class);

        foreach ($this->usuarioRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Usuario index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'usuario[nombre]' => 'Testing',
            'usuario[apellido]' => 'Testing',
            'usuario[fechanacimiento]' => 'Testing',
            'usuario[avatar]' => 'Testing',
        ]);

        self::assertResponseRedirects('/usuario');

        self::assertSame(1, $this->usuarioRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }

    public function testShow(): void
    {
        $fixture = new Usuario();
        $fixture->setNombre('My Title');
        $fixture->setApellido('My Title');
        $fixture->setFechanacimiento('My Title');
        $fixture->setAvatar('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Usuario');

        // Use assertions to check that the properties are properly displayed.
        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new Usuario();
        $fixture->setNombre('Value');
        $fixture->setApellido('Value');
        $fixture->setFechanacimiento('Value');
        $fixture->setAvatar('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'usuario[nombre]' => 'Something New',
            'usuario[apellido]' => 'Something New',
            'usuario[fechanacimiento]' => 'Something New',
            'usuario[avatar]' => 'Something New',
        ]);

        self::assertResponseRedirects('/usuario');

        $fixture = $this->usuarioRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNombre());
        self::assertSame('Something New', $fixture[0]->getApellido());
        self::assertSame('Something New', $fixture[0]->getFechanacimiento());
        self::assertSame('Something New', $fixture[0]->getAvatar());

        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new Usuario();
        $fixture->setNombre('Value');
        $fixture->setApellido('Value');
        $fixture->setFechanacimiento('Value');
        $fixture->setAvatar('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/usuario');
        self::assertSame(0, $this->usuarioRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }
}
